<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch8\Model\Adapter;

/**
 * Elasticsearch adapter
 */
class Elasticsearch extends \Magento\Elasticsearch\Model\Adapter\Elasticsearch
{
    /**
     * Reformat documents array to bulk format
     *
     * @param array $documents
     * @param string $indexName
     * @param string $action
     * @return array
     */
    protected function getDocsArrayInBulkIndexFormat(
        $documents,
        $indexName,
        $action = self::BULK_ACTION_INDEX
    ): array {
        $bulkArray = [
            'index' => $indexName,
            'body' => [],
            'refresh' => true,
        ];

        foreach ($documents as $id => $document) {
            $bulkArray['body'][] = [
                $action => [
                    '_id' => $id,
                    '_index' => $indexName
                ]
            ];

            if ($action == self::BULK_ACTION_INDEX) {
                $bulkArray['body'][] = $document;
            }
        }

        return $bulkArray;
    }
}
