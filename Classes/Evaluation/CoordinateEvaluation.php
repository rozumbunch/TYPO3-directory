<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Evaluation;

use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class CoordinateEvaluation
{
    public function returnFieldJS(): JavaScriptModuleInstruction
    {
        return JavaScriptModuleInstruction::create(
            '@rozumbunch/directory/form-engine-evaluation.js',
            'FormEngineEvaluation'
        );
    }

    public function evaluateFieldValue(string $value, string $isIn, bool &$set): ?string
    {
        [$minimum, $maximum] = self::parseRange($isIn, -180.0, 180.0);
        $result = self::parse($value, $minimum, $maximum);

        if ($result['empty']) {
            return null;
        }

        if (!$result['valid']) {
            $set = false;

            return $value;
        }

        return $result['normalized'];
    }

    /**
     * @param array{value?: mixed} $parameters
     */
    public function deevaluateFieldValue(array $parameters): string
    {
        $value = $parameters['value'] ?? '';
        if ($value === null || $value === '') {
            return '';
        }

        $string = trim((string)$value);
        if ($string === '0' || preg_match('/^-?0+(\.0+)?$/', $string) === 1) {
            return '';
        }

        if (preg_match('/^-?\d+\.\d+$/', $string) !== 1) {
            return $string;
        }

        return rtrim(rtrim($string, '0'), '.');
    }

    /**
     * @return array{empty: bool, valid: bool, normalized: ?string, error: string}
     */
    public static function parse(string $raw, float $minimum, float $maximum): array
    {
        $value = trim($raw);
        if ($value === '') {
            return ['empty' => true, 'valid' => true, 'normalized' => null, 'error' => ''];
        }

        if (preg_match('/^-?\d+[.,]\d+\s*,\s*-?\d+[.,]\d+$/', $value) === 1) {
            return ['empty' => false, 'valid' => false, 'normalized' => null, 'error' => 'format'];
        }

        $normalized = $value;
        if (preg_match('/^-?\d+,\d+$/', $value) === 1) {
            $normalized = str_replace(',', '.', $value);
        }

        if (preg_match('/^-?\d+\.\d{9,}$/', $normalized) === 1) {
            $normalized = self::format((float)$normalized);
        }

        if (preg_match('/^-?\d+(\.\d{1,8})?$/', $normalized) !== 1) {
            return ['empty' => false, 'valid' => false, 'normalized' => null, 'error' => 'format'];
        }

        $number = (float)$normalized;
        if ($number < $minimum || $number > $maximum) {
            return ['empty' => false, 'valid' => false, 'normalized' => null, 'error' => 'range'];
        }

        return ['empty' => false, 'valid' => true, 'normalized' => $normalized, 'error' => ''];
    }

    /**
     * @return array{0: float, 1: float}
     */
    private static function parseRange(string $isIn, float $defaultMinimum, float $defaultMaximum): array
    {
        if (preg_match('/^(-?\d+(?:\.\d+)?):(-?\d+(?:\.\d+)?)$/', trim($isIn), $matches) !== 1) {
            return [$defaultMinimum, $defaultMaximum];
        }

        return [(float)$matches[1], (float)$matches[2]];
    }

    private static function format(float $value): string
    {
        $formatted = number_format($value, 8, '.', '');

        return str_contains($formatted, '.') ? rtrim(rtrim($formatted, '0'), '.') : $formatted;
    }
}
