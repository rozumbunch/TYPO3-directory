<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Form;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

final class ApplyStoragePidFormDataProvider implements FormDataProviderInterface
{
    public function addData(array $result): array
    {
        $table = (string)($result['tableName'] ?? '');
        if (!str_starts_with($table, 'tx_directory_domain_model_')) {
            return $result;
        }

        $pid = $this->resolvePid($result);
        if ($pid <= 0) {
            return $result;
        }

        foreach ($result['processedTca']['columns'] ?? [] as $fieldName => $field) {
            $type = (string)($field['config']['type'] ?? '');
            $hasAddRecord = isset($field['config']['fieldControl']['addRecord']);
            if ($type !== 'group' && !$hasAddRecord) {
                continue;
            }

            if ($type === 'group') {
                $result['processedTca']['columns'][$fieldName]['config']['elementBrowserEntryPoints']['_default'] = (string)$pid;
            }
            if ($hasAddRecord) {
                $result['processedTca']['columns'][$fieldName]['config']['fieldControl']['addRecord']['options']['pid'] = $pid;
            }
            if (isset($field['config']['fieldControl']['listModule'])) {
                $result['processedTca']['columns'][$fieldName]['config']['fieldControl']['listModule']['options']['pid'] = $pid;
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $result
     */
    private function resolvePid(array $result): int
    {
        $site = $result['site'] ?? null;
        if ($site instanceof Site) {
            $storagePid = (int)$site->getSettings()->get('directory.storagePid', 0);
            if ($storagePid > 0) {
                return $storagePid;
            }
        }

        return (int)($result['effectivePid'] ?? 0);
    }
}
