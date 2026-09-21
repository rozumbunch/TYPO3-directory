<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class FlexFormValues
{
    public static function text(mixed $value, int $maxLength = 80): string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '';
        }

        if (mb_strlen($value) > $maxLength) {
            return mb_substr($value, 0, $maxLength);
        }

        return $value;
    }

    public static function uid(mixed $value): int
    {
        return self::uids($value)[0] ?? 0;
    }

    /**
     * @return list<int>
     */
    public static function uids(mixed $value): array
    {
        if (is_array($value)) {
            $raw = $value;
        } else {
            $raw = GeneralUtility::trimExplode(',', (string)$value, true);
        }

        $uids = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $item = (string)($item['uid'] ?? $item[0] ?? '');
            }
            $item = trim((string)$item);
            if ($item === '' || preg_match('/(\d+)$/', $item, $matches) !== 1) {
                continue;
            }
            $uid = (int)$matches[1];
            if ($uid > 0 && !in_array($uid, $uids, true)) {
                $uids[] = $uid;
            }
        }

        return $uids;
    }

    /**
     * @param list<string> $allowed
     * @param list<string> $fallback
     * @return list<string>
     */
    public static function list(mixed $value, array $allowed, array $fallback = []): array
    {
        $raw = is_array($value)
            ? $value
            : GeneralUtility::trimExplode(',', (string)$value, true);
        $items = [];
        foreach ($raw as $item) {
            $item = (string)$item;
            if (in_array($item, $allowed, true) && !in_array($item, $items, true)) {
                $items[] = $item;
            }
        }

        return $items !== [] ? $items : $fallback;
    }

    public static function bool(mixed $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_array($value)) {
            $value = $value[0] ?? $default;
        }

        return (int)$value === 1;
    }
}
