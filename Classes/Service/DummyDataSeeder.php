<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Dummy\DummyDirectoryFixtures;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class DummyDataSeeder
{
    private const TABLE_ORGANISATION = 'tx_directory_domain_model_organisation';
    private const TABLE_LOCATION = 'tx_directory_domain_model_location';
    private const TABLE_PERSON = 'tx_directory_domain_model_person';
    private const FOLDER_NAME = 'directory_dummy';

    /**
     * @var list<string>
     */
    private array $messages = [];

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly StorageRepository $storageRepository,
        private readonly SiteFinder $siteFinder,
    ) {}

    /**
     * @return list<string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function resolvePid(int $pidOption): int
    {
        if ($pidOption > 0) {
            return $pidOption;
        }

        foreach ($this->siteFinder->getAllSites() as $site) {
            $pid = (int)$site->getSettings()->get('directory.storagePid', 0);
            if ($pid > 0) {
                return $pid;
            }
        }

        throw new \RuntimeException(
            'Keine Storage-PID gefunden. Bitte --pid angeben oder directory.storagePid in den Site Settings setzen.',
            1758283200
        );
    }

    public function dummyRecordsExist(int $pid): bool
    {
        foreach ([self::TABLE_ORGANISATION, self::TABLE_LOCATION, self::TABLE_PERSON] as $table) {
            if ($this->fetchUids($table, $pid, true) !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function seed(int $pid, bool $wipe, bool $replace): array
    {
        $this->messages = [];

        if ($wipe) {
            $this->deleteRecords($pid, false);
            $this->cleanupOrphanedMmRelations();
            $this->messages[] = 'Bestehende Directory-Datensätze auf PID ' . $pid . ' gelöscht.';
        } elseif ($replace && $this->dummyRecordsExist($pid)) {
            $this->deleteRecords($pid, true);
            $this->cleanupOrphanedMmRelations();
            $this->messages[] = 'Vorhandene Dummy-Datensätze ersetzt.';
        } elseif ($this->dummyRecordsExist($pid)) {
            throw new \RuntimeException(
                'Dummy-Datensätze sind bereits vorhanden. Mit --replace neu anlegen oder --wipe den SysOrdner leeren.',
                1758283201
            );
        }

        $assets = $this->ensureAssets();
        $dataset = DummyDirectoryFixtures::get();
        $data = $this->buildDatamap($pid, $dataset, $assets);

        $dataHandler = $this->createDataHandler();
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            throw new \RuntimeException(
                'DataHandler-Fehler: ' . implode(' | ', $dataHandler->errorLog),
                1758283202
            );
        }

        $this->messages[] = sprintf(
            'Angelegt: %d Organisationen, %d Standorte, %d Personen.',
            count($dataset['organisations']),
            count($dataset['locations']),
            count($dataset['persons'])
        );
        $this->messages[] = 'Logo, Fallback-Profilbild und Dummy-PDFs liegen in fileadmin/directory_dummy/.';

        return $this->messages;
    }

    /**
     * @param array{
     *     locations: array<string, array<string, mixed>>,
     *     organisations: array<string, array<string, mixed>>,
     *     persons: array<string, array<string, mixed>>
     * } $dataset
     * @param array{logo: File, person: File, flyer: File, report: File} $assets
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function buildDatamap(int $pid, array $dataset, array $assets): array
    {
        $data = [
            self::TABLE_LOCATION => [],
            self::TABLE_ORGANISATION => [],
            self::TABLE_PERSON => [],
            'sys_file_reference' => [],
        ];
        $sorting = 256;
        $refCounter = 1;

        foreach ($dataset['locations'] as $newId => $record) {
            $pdfs = $this->extractPdfs($record);
            $record = $this->prepareRecord($record, $pid, $sorting);
            $sorting += 256;
            $mediaRefs = [];
            foreach ($pdfs as $index => $pdfKey) {
                $refId = sprintf('NEWFR%04d', $refCounter++);
                $this->addFileReference(
                    $data,
                    $refId,
                    $assets[$pdfKey]->getUid(),
                    self::TABLE_LOCATION,
                    $newId,
                    'media',
                    $pid,
                    $index + 1
                );
                $mediaRefs[] = $refId;
            }
            if ($mediaRefs !== []) {
                $record['media'] = implode(',', $mediaRefs);
            }
            $data[self::TABLE_LOCATION][$newId] = $record;
        }

        foreach ($dataset['organisations'] as $newId => $record) {
            $attachLogo = (bool)($record['_logo'] ?? false);
            $pdfs = $this->extractPdfs($record);
            $record = $this->prepareRecord($record, $pid, $sorting);
            $sorting += 256;
            if ($attachLogo) {
                $refId = sprintf('NEWFR%04d', $refCounter++);
                $this->addFileReference(
                    $data,
                    $refId,
                    $assets['logo']->getUid(),
                    self::TABLE_ORGANISATION,
                    $newId,
                    'logo',
                    $pid,
                    1
                );
                $record['logo'] = $refId;
            }
            $mediaRefs = [];
            foreach ($pdfs as $index => $pdfKey) {
                $refId = sprintf('NEWFR%04d', $refCounter++);
                $this->addFileReference(
                    $data,
                    $refId,
                    $assets[$pdfKey]->getUid(),
                    self::TABLE_ORGANISATION,
                    $newId,
                    'media',
                    $pid,
                    $index + 1
                );
                $mediaRefs[] = $refId;
            }
            if ($mediaRefs !== []) {
                $record['media'] = implode(',', $mediaRefs);
            }
            $data[self::TABLE_ORGANISATION][$newId] = $record;
        }

        foreach ($dataset['persons'] as $newId => $record) {
            $attachImage = (bool)($record['_image'] ?? false);
            $pdfs = $this->extractPdfs($record);
            $record = $this->prepareRecord($record, $pid, $sorting);
            $sorting += 256;
            if ($attachImage) {
                $refId = sprintf('NEWFR%04d', $refCounter++);
                $this->addFileReference(
                    $data,
                    $refId,
                    $assets['person']->getUid(),
                    self::TABLE_PERSON,
                    $newId,
                    'image',
                    $pid,
                    1
                );
                $record['image'] = $refId;
            }
            $mediaRefs = [];
            foreach ($pdfs as $index => $pdfKey) {
                $refId = sprintf('NEWFR%04d', $refCounter++);
                $this->addFileReference(
                    $data,
                    $refId,
                    $assets[$pdfKey]->getUid(),
                    self::TABLE_PERSON,
                    $newId,
                    'media',
                    $pid,
                    $index + 1
                );
                $mediaRefs[] = $refId;
            }
            if ($mediaRefs !== []) {
                $record['media'] = implode(',', $mediaRefs);
            }
            $data[self::TABLE_PERSON][$newId] = $record;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, mixed>
     */
    private function prepareRecord(array $record, int $pid, int $sorting): array
    {
        unset($record['_logo'], $record['_image'], $record['_pdfs']);
        $record['pid'] = $pid;
        $record['hidden'] = 0;
        $record['sys_language_uid'] = 0;
        $record['sorting'] = $sorting;
        foreach (['locations', 'business_units', 'contact_persons', 'organisations'] as $field) {
            if (($record[$field] ?? '') === '') {
                unset($record[$field]);
            }
        }

        return $record;
    }

    /**
     * @param array<string, mixed> $record
     * @return list<string>
     */
    private function extractPdfs(array &$record): array
    {
        $pdfs = $record['_pdfs'] ?? [];
        unset($record['_pdfs']);

        return is_array($pdfs) ? array_values($pdfs) : [];
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $data
     */
    private function addFileReference(
        array &$data,
        string $newId,
        int $fileUid,
        string $table,
        string $foreignNewId,
        string $fieldName,
        int $pid,
        int $sorting
    ): void {
        $data['sys_file_reference'][$newId] = [
            'uid_local' => $fileUid,
            'tablenames' => $table,
            'uid_foreign' => $foreignNewId,
            'fieldname' => $fieldName,
            'pid' => $pid,
            'table_local' => 'sys_file',
            'sys_language_uid' => 0,
            'hidden' => 0,
            'sorting_foreign' => $sorting,
        ];
    }

    /**
     * @return array{logo: File, person: File, flyer: File, report: File}
     */
    private function ensureAssets(): array
    {
        $storage = $this->storageRepository->getDefaultStorage();
        if (!$storage instanceof ResourceStorage) {
            throw new \RuntimeException('Kein Default-FAL-Storage (fileadmin) gefunden.', 1758283203);
        }
        $storage->setEvaluatePermissions(false);
        $folder = $this->ensureFolder($storage);

        return [
            'logo' => $this->addGeneratedFile(
                $folder,
                'dummy-logo.png',
                $this->packagedContent('dummy-logo.png') ?? $this->createLogoPng()
            ),
            'person' => $this->addGeneratedFile(
                $folder,
                'dummy-person.png',
                $this->packagedContent('dummy-person.png') ?? $this->createPersonPng()
            ),
            'flyer' => $this->addGeneratedFile($folder, 'dummy-steckbrief.pdf', $this->createPdf(
                'Dummy Steckbrief',
                'Platzhalter-PDF fuer Organisationen und Personen.'
            )),
            'report' => $this->addGeneratedFile($folder, 'dummy-jahresbericht.pdf', $this->createPdf(
                'Dummy Jahresbericht',
                'Zweites Platzhalter-PDF fuer Medienfelder.'
            )),
        ];
    }

    private function ensureFolder(ResourceStorage $storage): Folder
    {
        $root = $storage->getRootLevelFolder();
        if ($root->hasFolder(self::FOLDER_NAME)) {
            return $root->getSubfolder(self::FOLDER_NAME);
        }

        return $storage->createFolder(self::FOLDER_NAME, $root);
    }

    private function addGeneratedFile(Folder $folder, string $fileName, string $binary): File
    {
        $tempFile = Environment::getVarPath() . '/transient/directory-' . $fileName;
        $directory = dirname($tempFile);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Temp-Verzeichnis konnte nicht angelegt werden: ' . $directory, 1758283204);
        }
        if (file_put_contents($tempFile, $binary) === false) {
            throw new \RuntimeException('Dummy-Datei konnte nicht geschrieben werden: ' . $tempFile, 1758283205);
        }

        $file = $folder->addFile($tempFile, $fileName, DuplicationBehavior::REPLACE);
        if (!$file instanceof File) {
            throw new \RuntimeException('FAL-Datei konnte nicht angelegt werden: ' . $fileName, 1758283206);
        }

        return $file;
    }

    private function packagedContent(string $fileName): ?string
    {
        $path = ExtensionManagementUtility::extPath('directory') . 'Resources/Public/Content/' . $fileName;
        if (!is_file($path)) {
            return null;
        }

        $binary = file_get_contents($path);
        if ($binary === false || $binary === '') {
            return null;
        }

        return $binary;
    }

    private function createLogoPng(): string
    {
        $size = 1024;
        $image = imagecreatetruecolor($size, $size);
        if ($image === false) {
            throw new \RuntimeException('GD konnte das Logo nicht erzeugen.', 1758283207);
        }
        $background = (int)imagecolorallocate($image, 236, 236, 236);
        $mid = (int)imagecolorallocate($image, 154, 154, 154);
        $light = (int)imagecolorallocate($image, 207, 207, 207);
        imagefilledrectangle($image, 0, 0, $size - 1, $size - 1, $background);

        $roof = [512, 220, 250, 470, 774, 470];
        imagefilledpolygon($image, $roof, $mid);
        imagefilledrectangle($image, 310, 470, 714, 820, $mid);
        imagefilledrectangle($image, 455, 620, 569, 820, $light);
        imagefilledrectangle($image, 360, 545, 445, 630, $light);
        imagefilledrectangle($image, 579, 545, 664, 630, $light);

        return $this->pngToString($image);
    }

    private function createPersonPng(): string
    {
        $image = imagecreatetruecolor(512, 512);
        if ($image === false) {
            throw new \RuntimeException('GD konnte das Profilbild nicht erzeugen.', 1758283208);
        }
        $blue = (int)imagecolorallocate($image, 29, 79, 122);
        $white = (int)imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 511, 511, $blue);
        imagefilledellipse($image, 256, 170, 160, 160, $white);
        imagefilledarc($image, 256, 520, 340, 420, 180, 360, $white, IMG_ARC_PIE);
        imagestring($image, 5, 214, 24, 'PROFIL', $white);

        return $this->pngToString($image);
    }

    /**
     * @param \GdImage $image
     */
    private function pngToString($image): string
    {
        ob_start();
        imagepng($image);
        imagedestroy($image);
        $binary = ob_get_clean();
        if ($binary === false || $binary === '') {
            throw new \RuntimeException('PNG konnte nicht serialisiert werden.', 1758283209);
        }

        return $binary;
    }

    private function createPdf(string $title, string $body): string
    {
        $content = 'BT /F1 24 Tf 72 760 Td (' . $this->pdfEscape($title) . ') Tj 0 -36 Td /F1 12 Tf ('
            . $this->pdfEscape($body) . ') Tj ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1, $count = count($objects); $i <= $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";

        return $pdf;
    }

    private function pdfEscape(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    private function deleteRecords(int $pid, bool $dummyOnly): void
    {
        $cmd = [];
        foreach ([self::TABLE_PERSON, self::TABLE_ORGANISATION, self::TABLE_LOCATION] as $table) {
            foreach ($this->fetchUids($table, $pid, $dummyOnly) as $uid) {
                $cmd[$table][$uid] = ['delete' => 1];
            }
        }
        if ($cmd === []) {
            return;
        }

        $dataHandler = $this->createDataHandler();
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();
        if ($dataHandler->errorLog !== []) {
            throw new \RuntimeException(
                'Löschen fehlgeschlagen: ' . implode(' | ', $dataHandler->errorLog),
                1758283210
            );
        }
    }

    private function cleanupOrphanedMmRelations(): void
    {
        $pairs = [
            'tx_directory_organisation_organisation_mm' => [self::TABLE_ORGANISATION, self::TABLE_ORGANISATION],
            'tx_directory_organisation_location_mm' => [self::TABLE_ORGANISATION, self::TABLE_LOCATION],
            'tx_directory_organisation_person_mm' => [self::TABLE_ORGANISATION, self::TABLE_PERSON],
            'tx_directory_person_organisation_mm' => [self::TABLE_PERSON, self::TABLE_ORGANISATION],
            'tx_directory_person_location_mm' => [self::TABLE_PERSON, self::TABLE_LOCATION],
        ];

        foreach ($pairs as $mmTable => [$localTable, $foreignTable]) {
            $this->connectionPool->getConnectionForTable($mmTable)->executeStatement(
                'DELETE mm FROM ' . $mmTable . ' mm'
                . ' LEFT JOIN ' . $localTable . ' local_rec ON local_rec.uid = mm.uid_local AND local_rec.deleted = 0'
                . ' LEFT JOIN ' . $foreignTable . ' foreign_rec ON foreign_rec.uid = mm.uid_foreign AND foreign_rec.deleted = 0'
                . ' WHERE local_rec.uid IS NULL OR foreign_rec.uid IS NULL'
            );
        }
    }

    /**
     * @return list<int>
     */
    private function fetchUids(string $table, int $pid, bool $dummyOnly): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            );
        if ($dummyOnly) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    'identifier',
                    $queryBuilder->createNamedParameter(DummyDirectoryFixtures::ID_PREFIX . '%')
                )
            );
        }

        return array_map('intval', $queryBuilder->executeQuery()->fetchFirstColumn());
    }

    private function createDataHandler(): DataHandler
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->bypassAccessCheckForRecords = true;

        return $dataHandler;
    }
}
