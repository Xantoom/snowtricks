<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Snowtrick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
	private Generator $faker;

	private array $users = [];
	private array $snowtricks = [];

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

		$this->createUsers($manager);
		$this->createSnowtricks($manager);
		$this->createComments($manager);

        $manager->flush();
    }

	private function createUsers(ObjectManager $manager): void
	{
		$user = new User();
		$user
			->setEmail('user@test.fr')
			->setFirstname('User')
			->setLastname('User')
			->setPassword('password')
			->setRoles(['ROLE_USER'])
		;
		$manager->persist($user);
		$this->users[] = $user;

		$admin = new User();
		$admin
			->setEmail('admin@test.fr')
			->setFirstname('Admin')
			->setLastname('Admin')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN'])
		;
		$manager->persist($admin);
		$this->users[] = $admin;

		$nbUsers = 10;
		for ($i = 0; $i < $nbUsers; $i++) {
			$user = new User();
			$user
				->setEmail($this->faker->email)
				->setFirstname($this->faker->firstName)
				->setLastname($this->faker->lastName)
				->setPassword('password')
				->setRoles(['ROLE_USER'])
			;
			$manager->persist($user);
			$this->users[] = $user;
		}
		$manager->flush();
	}

	private function createSnowtricks(ObjectManager $manager): void
	{
		$nbTricks = 50;
		for ($i = 0; $i < $nbTricks; $i++) {
			$snowtrick = new Snowtrick();
			$snowtrick
				->setCreatedAt(new \DateTimeImmutable('-1 year'))
				->setCreatedBy($this->users[array_rand($this->users)])
				->setDescription($this->faker->text)
				->setName($this->faker->word)
				->setUpdatedAt($this->faker->boolean(30) ? new \DateTimeImmutable('-1 month') : null)
			;
			$manager->persist($snowtrick);
			$this->snowtricks[] = $snowtrick;
		}

		$manager->flush();
	}

	private function createComments(ObjectManager $manager): void
	{
		$nbComments = 100;
		for ($i = 0; $i < $nbComments; $i++) {
			$comment = new Comment();
			$comment
				->setCreatedAt(new \DateTimeImmutable('-1 year'))
				->setCreatedBy($this->users[array_rand($this->users)])
				->setSnowtrick($this->snowtricks[array_rand($this->snowtricks)])
				->setEditedAt($this->faker->boolean(30) ? new \DateTimeImmutable('-1 month') : null)
				->setContent($this->faker->text)
			;
			$manager->persist($comment);
		}

		$manager->flush();
	}
}
