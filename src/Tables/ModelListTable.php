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

class ModelListTable extends AbstractDataTable
{
    protected function initColumns(): array
    {
        return [
            'id' => [
                'field' => 'id',
                'field_sort' => 't.id',
                'label' => 'ID',
                'width' => '50px',
                'centered' => false,
                'visible' => true,
                'sortable' => true,
                //'class' => 'custom-class',
                //'template' => '<span class="badge bg-secondary">{{ data }}</span>',
                //'background' => '#333333',
                //'roles' => ['ROLE_ACCOUNT_LIST'],
                //'export_template' => '',
                'filters' => [
                    DataTableFilters::number('id', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('m.id = :id')->setParameter('id', $data['id']);
                    }, 'ID'),
                ],
            ],
            'label' => [
                'field' => 'label',
                'field_sort' => 'm.label',
                'label' => $this->trans->trans('testing.model_label'),
                'sortable' => true,
                'filters' => [
                    DataTableFilters::text('label', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('m.label = :label')->setParameter('label', $data['label']);
                    }, $this->trans->trans('testing.model_label')),
                ],
            ],
            'tags' => [
                'field' => 'tags',
                'label' => $this->trans->trans('testing.model_tags'),
                'sortable' => false,
                'template' => "<span>{{ data.tags|join(', ') }}</span>",
                'filters' => [
                    DataTableFilters::number('tags', static function (QueryBuilder $query, array $data) {
                        $query->andWhere($query->expr()->like('m.tags', ':tags'))->setParameter('tags', '%' . $data['tags'] . '%');
                    }, $this->trans->trans('testing.model_tags')),
                ],
            ],
            'createdAt' => [
                'field' => 'createdAt',
                'field_sort' => 'm.createdAt',
                'label' => $this->trans->trans('testing.created_at'),
                'sortable' => true,
                'template' => '<span>{{ dateISOTime(data.createdAt) }}</span>',
                'export_template' => fn (\DateTimeInterface $date) => $date->format('d.m.Y H:i'),
                'filters' => [
                    DataTableFilters::dateRange('createdAt', static function (QueryBuilder $query, array $data) {
                        if (isset($data['createdAt']['start'])) {
                            $query->andWhere('m.createdAt >= :createdStart')->setParameter('createdStart', new \DateTime($data['createdAt']['start']));
                        }
                        if (isset($data['createdAt']['end'])) {
                            $query->andWhere('m.createdAt < :createdEnd')->setParameter('createdEnd', new \DateTime($data['createdAt']['end']));
                        }
                    }),
                ],
            ],
            'updatedAt' => [
                'field' => 'updatedAt',
                'field_sort' => 'm.updatedAt',
                'label' => $this->trans->trans('testing.updated_at'),
                'sortable' => true,
                'template' => '<span>{{ dateRelative(data.updatedAt) }}</span>',
                'export_template' => fn (\DateTimeInterface $date) => $date->format('d.m.Y H:i'),
                'filters' => [
                    DataTableFilters::dateRange('updatedAt', static function (QueryBuilder $query, array $data) {
                        if (isset($data['updatedAt']['start'])) {
                            $query->andWhere('m.updatedAt >= :lastStart')->setParameter('updatedAtStart', new \DateTime($data['updatedAt']['start']));
                        }
                        if (isset($data['updatedAt']['end'])) {
                            $query->andWhere('m.updatedAt < :lastEnd')->setParameter('updatedAtEnd', new \DateTime($data['updatedAt']['end']));
                        }
                    }),
                ],
            ],
        ];
    }
}
