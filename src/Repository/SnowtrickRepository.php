<?php

namespace App\Repository;

use App\Entity\Snowtrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Snowtrick>
 */
class SnowtrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snowtrick::class);
    }

	public function save(Snowtrick $snowtrick): void
	{
		$this->getEntityManager()->persist($snowtrick);
		$this->getEntityManager()->flush();
	}

	public function remove(Snowtrick $snowtrick): void
	{
		$this->getEntityManager()->remove($snowtrick);
		$this->getEntityManager()->flush();
	}
}
