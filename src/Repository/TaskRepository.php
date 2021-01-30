<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tienvx\Bundle\MbtBundle\Entity\Task;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function countTasksByRevisions(array $revisionIds): array
    {
        $qb = $this->_em->createQueryBuilder();
        $result = $this->createQueryBuilder('t')
            ->where($qb->expr()->in('t.modelRevision', ':revisions'))
            ->setParameter('revisions', $revisionIds)
            ->select('COUNT(t.id) as tasks, t.modelRevision as revision')
            ->groupBy('t.modelRevision')
            ->getQuery()
            ->getArrayResult();

        return array_column($result, 'tasks', 'revision');
    }
}
