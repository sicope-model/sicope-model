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

class TaskListTable extends AbstractDataTable
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
                        $query->andWhere('t.id = :id')->setParameter('id', $data['id']);
                    }, 'ID'),
                ],
            ],
            'title' => [
                'field' => 'title',
                'field_sort' => 't.title',
                'label' => $this->trans->trans('testing.task_title'),
                'sortable' => true,
                'filters' => [
                    DataTableFilters::text('title', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('t.title = :title')->setParameter('title', $data['title']);
                    }, $this->trans->trans('testing.task_title')),
                ],
            ],
            'model' => [
                'field' => 'modelRevision.model',
                'field_sort' => 't.modelRevision.model.label',
                'label' => $this->trans->trans('testing.task_model'),
                'sortable' => true,
                'template' => '{% if task.modelRevision.model is not null %}
                                <a href="{{ path(\'testing.model_view\', { \'model\': data.modelRevision.model.id }) }}">
                                    {{ task.modelRevision.model.label }}
                                    {% if task.modelRevision.id is not same as(task.modelRevision.model.activeRevision.id) %}
                                        <span class="badge badge-warning">{{ \'testing.outdated\'|trans }}</span>
                                    {% endif %}
                                </a>
                            {% else %}
                                <span class="badge badge-danger">{{ \'testing.deleted\'|trans }}</span>
                            {% endif %}',
                'filters' => [
                    DataTableFilters::text('modelRevision.model.label', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('t.modelRevision.model.label = :model')->setParameter('model', $data['model']);
                    }, $this->trans->trans('testing.task_model')),
                ],
            ],
            'status' => [
                'field' => 'running',
                'field_sort' => 't.running',
                'label' => $this->trans->trans('testing.status'),
                'sortable' => true,
                'template' => '<span class="badge bg-success" v-if="data.running">' . $this->trans->trans('testing.task_running') . '</span>
                               <span class="badge bg-secondary" v-else>' . $this->trans->trans('testing.task_stopped') . '</span>',
                'export_template' => fn ($data) => $data ? $this->trans->trans('testing.task_running') : $this->trans->trans('testing.task_stopped'),
                'filters' => [
                    DataTableFilters::switch('running', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('t.running = :running')->setParameter('running', (bool) json_decode(strtolower($data['running'])));
                    }, $this->trans->trans('testing.status')),
                ],
            ],
            'bugs' => [
                'field' => 'bugs',
                'field_sort' => 'COUNT(t.bugs)',
                'label' => $this->trans->trans('testing.bugs'),
                'sortable' => true,
                'template' => '<span>{{ data.bugs|length }}</span>',
                'filters' => [
                    DataTableFilters::number('bugs', static function (QueryBuilder $query, array $data) {
                        $query->andWhere('COUNT(t.bugs) = :bugs')->setParameter('bugs', $data['bugs']);
                    }, $this->trans->trans('testing.bugs')),
                ],
            ],
            'createdAt' => [
                'field' => 'createdAt',
                'field_sort' => 't.createdAt',
                'label' => $this->trans->trans('testing.created_at'),
                'sortable' => true,
                'template' => '<span>{{ dateISOTime(data.createdAt) }}</span>',
                'export_template' => fn (\DateTimeInterface $date) => $date->format('d.m.Y H:i'),
                'filters' => [
                    DataTableFilters::dateRange('createdAt', static function (QueryBuilder $query, array $data) {
                        if (isset($data['createdAt']['start'])) {
                            $query->andWhere('t.createdAt >= :createdStart')->setParameter('createdStart', new \DateTime($data['createdAt']['start']));
                        }
                        if (isset($data['createdAt']['end'])) {
                            $query->andWhere('t.createdAt < :createdEnd')->setParameter('createdEnd', new \DateTime($data['createdAt']['end']));
                        }
                    }),
                ],
            ],
            'updatedAt' => [
                'field' => 'updatedAt',
                'field_sort' => 't.updatedAt',
                'label' => $this->trans->trans('testing.updated_at'),
                'sortable' => true,
                'template' => '<span>{{ dateRelative(data.updatedAt) }}</span>',
                'export_template' => fn (\DateTimeInterface $date) => $date->format('d.m.Y H:i'),
                'filters' => [
                    DataTableFilters::dateRange('updatedAt', static function (QueryBuilder $query, array $data) {
                        if (isset($data['updatedAt']['start'])) {
                            $query->andWhere('t.updatedAt >= :lastStart')->setParameter('updatedAtStart', new \DateTime($data['updatedAt']['start']));
                        }
                        if (isset($data['updatedAt']['end'])) {
                            $query->andWhere('t.updatedAt < :lastEnd')->setParameter('updatedAtEnd', new \DateTime($data['updatedAt']['end']));
                        }
                    }),
                ],
            ],
        ];
    }
}
