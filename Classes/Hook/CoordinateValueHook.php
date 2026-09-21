<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Hook;

use Rozumbunch\Directory\Configuration\Tca;
use Rozumbunch\Directory\Evaluation\CoordinateEvaluation;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\SysLog\Action\Database as SystemLogDatabaseAction;
use TYPO3\CMS\Core\SysLog\Error as SystemLogErrorClassification;
use TYPO3\CMS\Core\Utility\MathUtility;

final class CoordinateValueHook
{
    /**
     * @param array<string, mixed> $incomingFieldArray
     */
    public function processDatamap_preProcessFieldArray(
        array &$incomingFieldArray,
        string $table,
        int|string $id,
        DataHandler $dataHandler
    ): void {
        if ($table !== 'tx_directory_domain_model_location') {
            return;
        }

        $fields = [
            'latitude' => [
                'min' => -90.0,
                'max' => 90.0,
                'labelKey' => 'tx_directory_domain_model_location.latitude',
                'example' => '50.1109',
            ],
            'longitude' => [
                'min' => -180.0,
                'max' => 180.0,
                'labelKey' => 'tx_directory_domain_model_location.longitude',
                'example' => '8.6821',
            ],
        ];

        foreach ($fields as $field => $config) {
            if (!array_key_exists($field, $incomingFieldArray)) {
                continue;
            }

            $raw = trim((string)$incomingFieldArray[$field]);
            $result = CoordinateEvaluation::parse($raw, $config['min'], $config['max']);

            if ($result['empty']) {
                $incomingFieldArray[$field] = null;
                continue;
            }

            if ($result['valid']) {
                $incomingFieldArray[$field] = $result['normalized'];
                continue;
            }

            $messageKey = $result['error'] === 'range'
                ? 'coordinate.error.range'
                : 'coordinate.error.format';

            $dataHandler->log(
                $table,
                MathUtility::canBeInterpretedAsInteger($id) ? (int)$id : 0,
                SystemLogDatabaseAction::UPDATE,
                null,
                SystemLogErrorClassification::USER_ERROR,
                $this->translate($messageKey),
                null,
                [
                    'value' => $raw,
                    'field' => $this->translate($config['labelKey']),
                    'example' => $config['example'],
                    'min' => (string)$config['min'],
                    'max' => (string)$config['max'],
                ]
            );

            unset($incomingFieldArray[$field]);
        }
    }

    private function translate(string $key): string
    {
        $languageService = $GLOBALS['LANG'] ?? null;
        if (!$languageService instanceof LanguageService) {
            return $key;
        }

        $label = trim($languageService->sL(Tca::LL . $key));

        return $label !== '' ? $label : $key;
    }
}
