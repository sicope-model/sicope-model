<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Tables;

use App\DataTable\AbstractDataTable;
use App\DataTable\DataTableFilters;
use Doctrine\ORM\QueryBuilder;
use Pd\ActivityBundle\Entity\ActivityLog;

class ActivityLogHttpTable extends AbstractDataTable
{
    protected function initColumns(): array
    {
        return [
            'id' => [
                'field' => 'id',
                'field_sort' => 'q.id',
                'label' => 'ID',
                'width' => '50px',
                'centered' => false,
                'visible' => true,
                'sortable' => true,
                'filters' => [
                    DataTableFilters::number('id', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('q.id = :uid')->setParameter('uid', $data['id']);
                    }, 'ID'),
                ],
            ],
            'method' => [
                'field' => 'method',
                'field_sort' => 'q.method',
                'label' => 'Method',
                'sortable' => true,
                'filters' => [
                    DataTableFilters::select('method', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('q.method = :method')->setParameter('method', $data['method']);
                    }, array_flip(ActivityLog::METHODS)),
                ],
            ],
            'uri' => [
                'field' => 'uri',
                'field_sort' => 'q.uri',
                'label' => 'Uri',
                'sortable' => true,
                'filters' => [
                    DataTableFilters::email('uri', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('q.uri LIKE :uri')->setParameter('uri', "{$data['uri']}%");
                    }, 'Uri'),
                ],
            ],
            'clientIp' => [
                'field' => 'clientIp',
                'field_sort' => 'q.clientIp',
                'label' => 'Client IP',
                'sortable' => true,
                'filters' => [
                    DataTableFilters::email('clientIp', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('q.clientIp = :clientIp')->setParameter('clientIp', $data['clientIp']);
                    }, 'Client IP'),
                ],
            ],
            'locale' => [
                'field' => 'locale',
                'field_sort' => 'q.locale',
                'label' => 'Dil',
                'sortable' => true,
                'filters' => [
                    DataTableFilters::email('locale', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('q.locale = :locale')->setParameter('locale', $data['locale']);
                    }, 'Dil'),
                ],
            ],
            'owner' => [
                'field' => 'owner',
                'field_sort' => 'q.owner',
                'label' => 'İşlem Yapan',
                'sortable' => false,
                'filters' => [
                    DataTableFilters::email('owner', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('IDENTITY(q.owner) = :owner')->setParameter('owner', $data['owner']);
                    }, 'İşlem Yapan'),
                ],
            ],
            'createdAt' => [
                'field' => 'createdAt',
                'field_sort' => 'q.createdAt',
                'label' => 'Oluşturulma',
                'sortable' => true,
                'template' => '<span>{{ dateISOTime(data.createdAt) }}</span>',
                'filters' => [
                    DataTableFilters::dateRange('createdAt', static function (QueryBuilder $query, array $data) {
                        if (isset($data['createdAt']['start'])) {
                            $query->andWhere('q.createdAt >= :createdStart')->setParameter('createdStart', new \DateTime($data['createdAt']['start']));
                        }
                        if (isset($data['createdAt']['end'])) {
                            $query->andWhere('q.createdAt < :createdEnd')->setParameter('createdEnd', new \DateTime($data['createdAt']['end']));
                        }
                    }),
                ],
            ],
        ];
    }
}
