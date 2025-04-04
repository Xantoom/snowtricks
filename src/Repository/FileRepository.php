<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

	public function save(File $file): void
	{
		$this->getEntityManager()->persist($file);
		$this->getEntityManager()->flush();
	}

	public function remove(File $file): void
	{
		$this->getEntityManager()->remove($file);
		$this->getEntityManager()->flush();
	}
}
