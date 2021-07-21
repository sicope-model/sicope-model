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

namespace App\Controller\Admin;

use App\Entity\Account\DataTable;
use App\Repository\Account\DataTableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DataTableController extends AbstractController
{
    /**
     * Get User Table Data.
     */
    #[Route('/data-table', name: 'admin.data_table', methods: ['GET'])]
    public function list(Request $request, DataTableRepository $repo): JsonResponse
    {
        if ($request->request->has('table')) {
            throw $this->createNotFoundException('Tablo adı gerekli!');
        }

        $table = $repo->findOneBy([
                'owner' => $this->getUser(),
                'name' => $request->get('table'),
            ]) ?? new DataTable();

        return $this->json($table, 200, [], ['groups' => 'default']);
    }

    /**
     * Data Table Save User Configuration.
     */
    #[Route('/data-table', name: 'admin.data_table_post', methods: ['POST'])]
    public function save(Request $request, DataTableRepository $repo): JsonResponse
    {
        if ($request->request->has('table')) {
            throw $this->createNotFoundException('Tablo adı gerekli!');
        }

        $table = $repo->createOrUpdate($this->getUser(), $request);

        return $this->json($table, 200, [], ['groups' => 'default']);
    }
}
