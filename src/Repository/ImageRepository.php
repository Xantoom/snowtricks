<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

	public function save(Image $image): void
	{
		$this->getEntityManager()->persist($image);
		$this->getEntityManager()->flush();
	}

	public function remove(Image $image): void
	{
		$this->getEntityManager()->remove($image);
		$this->getEntityManager()->flush();
	}
}
