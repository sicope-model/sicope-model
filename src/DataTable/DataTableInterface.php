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

namespace App\DataTable;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface DataTableInterface
{
    public function getName(): string | int;

    public function getColumns(): array;

    public function getColumnsJson(): string;

    public function handleQueryBuilder(QueryBuilder $queryBuilder): self;

    public function handleRequest(Request $request): self;

    public function export(): StreamedResponse;
}
