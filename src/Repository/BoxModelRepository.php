<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\BoxModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoxModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoxModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoxModel[]    findAll()
 * @method BoxModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxModelRepository extends ServiceEntityRepository
{
    /**
     * Constructor
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoxModel::class);
    }

    /**
     * Sorted by display label
     *
     * @return BoxModel[] Returns an array of BoxModel objects
     */
    public function getSortedByDisplayLabel()
    {
        return $this->findBy([], ['label' => 'ASC']);
    }

    /**
     * Sorted by id
     *
     * @return BoxModel[] Returns an array of BoxModel objects
     */
    public function getSortedById()
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
