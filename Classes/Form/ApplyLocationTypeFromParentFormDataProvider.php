<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Form;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

final class ApplyLocationTypeFromParentFormDataProvider implements FormDataProviderInterface
{
    private const PARENT_TYPES = [
        'tx_directory_domain_model_person' => 'private',
        'tx_directory_domain_model_organisation' => 'company',
    ];

    public function addData(array $result): array
    {
        if (
            ($result['tableName'] ?? '') !== 'tx_directory_domain_model_location'
            || ($result['command'] ?? '') !== 'new'
        ) {
            return $result;
        }

        $parent = $this->resolveParent($result);
        $locationType = self::PARENT_TYPES[$parent['table']] ?? '';
        if ($locationType === '' || $parent['field'] !== 'locations') {
            return $result;
        }

        $result['databaseRow']['location_type'] = $locationType;
        $result['processedTca']['columns']['location_type']['config']['default'] = $locationType;
        $result['processedTca']['columns']['location_type']['config']['type'] = 'passthrough';

        foreach ($result['processedTca']['types'] ?? [] as $type => $config) {
            if (!isset($config['showitem']) || !is_string($config['showitem'])) {
                continue;
            }

            $result['processedTca']['types'][$type]['showitem'] = str_replace(
                '--palette--;;type,',
                '',
                $config['showitem']
            );
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $result
     * @return array{table: string, field: string, uid: string}
     */
    private function resolveParent(array $result): array
    {
        $parent = $this->parentFromQuery($this->queryFromUrl((string)($result['returnUrl'] ?? '')));
        if ($parent['table'] !== '') {
            return $parent;
        }

        $request = $result['request'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return $parent;
        }

        $queryParams = $request->getQueryParams();
        $parent = $this->parentFromQuery($queryParams);
        if ($parent['table'] !== '') {
            return $parent;
        }

        return $this->parentFromQuery($this->queryFromUrl((string)($queryParams['returnUrl'] ?? '')));
    }

    /**
     * @return array{table: string, field: string, uid: string}
     */
    private function parentFromQuery(array $query): array
    {
        $wizard = $query['P'] ?? [];
        if (!is_array($wizard)) {
            return ['table' => '', 'field' => '', 'uid' => ''];
        }

        return [
            'table' => (string)($wizard['table'] ?? ''),
            'field' => (string)($wizard['field'] ?? ''),
            'uid' => (string)($wizard['uid'] ?? ''),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function queryFromUrl(string $url): array
    {
        if ($url === '') {
            return [];
        }

        $query = (string)(parse_url($url, PHP_URL_QUERY) ?? '');
        if ($query === '' && str_contains($url, 'P%5B')) {
            $query = (string)(parse_url(urldecode($url), PHP_URL_QUERY) ?? '');
        }
        if ($query === '') {
            return [];
        }

        parse_str($query, $params);

        return is_array($params) ? $params : [];
    }
}
