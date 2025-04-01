<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Snowtrick;
use App\Entity\User;
use App\Enum\SnowtrickCategories;
use App\Service\FileManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
	private Generator $faker;
	private array $users = [];
	private array $snowtricks = [];

	public function __construct(
		private readonly UserPasswordHasherInterface $hasher,
		private readonly FileManager $fileManager,
	) {
		$this->faker = Factory::create();
	}

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

		$this->createUsers($manager);
		$this->createSnowtricks($manager);
		$this->createComments($manager);
		$this->createFiles($manager);

        $manager->flush();
    }

	private function createUsers(ObjectManager $manager): void
	{
        $password = 'password';

		$user = new User();
		$user
			->setEmail('user@test.fr')
			->setFirstname('User')
			->setLastname('User')
			->setPassword($this->hasher->hashPassword($user, $password))
			->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
		;
		$manager->persist($user);
		$this->users[] = $user;

		$admin = new User();
		$admin
			->setEmail('admin@test.fr')
			->setFirstname('Admin')
			->setLastname('Admin')
			->setPassword($this->hasher->hashPassword($user, $password))
			->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
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
				->setPassword($this->hasher->hashPassword($user, $password))
				->setRoles(['ROLE_USER'])
                ->setIsVerified($this->faker->boolean(80))
			;
			$manager->persist($user);
			$this->users[] = $user;
		}
		$manager->flush();
	}

	private function createSnowtricks(ObjectManager $manager): void
	{
        $snowtrickData = [
            [
                'name' => 'Ollie',
                'description' => 'The fundamental trick in snowboarding. It involves jumping off the ground by pressing on the tail of the board, then pulling your knees toward your chest to gain height. It\'s the foundation for many other tricks.'
            ],
            [
                'name' => 'Nollie',
                'description' => 'A variant of the Ollie where the rider presses on the nose (front) of the board rather than the tail to take off. This technique allows for varied approaches on different features.'
            ],
            [
                'name' => 'Frontside 180',
                'description' => 'A 180-degree rotation where the rider turns toward the front (facing the slope). This basic rotation is essential for progressing to more complex rotations.'
            ],
            [
                'name' => 'Backside 180',
                'description' => 'A 180-degree rotation where the rider turns toward the back (back to the slope). More difficult to master than the Frontside because you momentarily lose sight of the landing.'
            ],
            [
                'name' => 'Frontside 360',
                'description' => 'A complete 360-degree rotation by turning toward the front. This trick requires good mastery of balance and rotation in the air.'
            ],
            [
                'name' => 'Backside 360',
                'description' => 'A complete 360-degree rotation by turning toward the back. A more technical variant of the 360 as the rider loses sight of their landing during part of the rotation.'
            ],
            [
                'name' => 'Indy Grab',
                'description' => 'A classic grab where the rider grabs the frontside edge of the board between the bindings with their back hand during a jump. Probably the most commonly executed grab.'
            ],
            [
                'name' => 'Mute Grab',
                'description' => 'A grab where the rider grabs the frontside edge of the board between the bindings with their front hand during a jump. This grab helps stabilize frontside rotations.'
            ],
            [
                'name' => 'Method',
                'description' => 'One of the most iconic grabs in snowboarding. The rider grabs the heelside edge of the board with their back hand while arching their back and pulling up their knees. Style and amplitude are essential.'
            ],
            [
                'name' => 'Nose Grab',
                'description' => 'The rider grabs the front (nose) of the board with their front hand. This grab requires good flexibility and is often combined with various rotations.'
            ],
            [
                'name' => 'Tail Grab',
                'description' => 'The rider grabs the back (tail) of the board with their back hand. This classic grab is often used to add style to a simple jump.'
            ],
            [
                'name' => 'Stalefish',
                'description' => 'A grab where the rider grabs the heelside edge of the board between the bindings with their back hand, behind their back. An elegant position that requires good flexibility.'
            ],
            [
                'name' => 'Japan Air',
                'description' => 'A grab where the rider grabs the front of the board on the toeside with their front hand, while pulling the front leg upward. This trick has a unique and recognizable aesthetic.'
            ],
            [
                'name' => 'Boardslide',
                'description' => 'A rail trick where the rider slides perpendicularly on a rail or box with the board at 90 degrees to the obstacle, between the bindings. It\'s one of the first rail tricks that riders learn.'
            ],
            [
                'name' => 'Lipslide',
                'description' => 'Similar to a boardslide, but the rider approaches the rail from the other side and must pass their board over the obstacle before sliding. More technical than the classic boardslide.'
            ],
            [
                'name' => 'Frontside Boardslide',
                'description' => 'A boardslide where the rider approaches the rail or box with the rail on the frontside (facing them). Usually the first slide learned on a rail.'
            ],
            [
                'name' => 'Backside Boardslide',
                'description' => 'A boardslide where the rider approaches the rail or box with the rail on the backside (behind them). More difficult because the rider must turn more to align with the rail.'
            ],
            [
                'name' => '50-50',
                'description' => 'A basic rail trick where the rider slides on a rail or box with the board parallel to the obstacle, both bindings on either side of the rail. It\'s generally the first rail trick learned.'
            ],
            [
                'name' => 'Butter',
                'description' => 'A ground technique where the rider pivots the board on the snow by balancing on the nose or tail. This fluid trick gives the impression that the rider is "spreading butter" on the snow.'
            ],
            [
                'name' => 'Nose Press',
                'description' => 'A maneuver where the rider balances on the front of the board (nose) while lifting the back. Can be executed on flat terrain or on obstacles like boxes.'
            ],
            [
                'name' => 'Tail Press',
                'description' => 'A maneuver where the rider balances on the back of the board (tail) while lifting the front. Often used as a transition trick or in combination with rotations.'
            ],
            [
                'name' => 'Cork',
                'description' => 'An off-axis rotation style where the head passes below the level of the feet. Cork rotations have become essential in modern snowboarding, adding complexity and style to jumps.'
            ],
            [
                'name' => 'Frontside 540',
                'description' => 'A one-and-a-half rotation (540 degrees) turning toward the front. This intermediate-advanced trick is often combined with different grabs for more style.'
            ],
            [
                'name' => 'Backside 540',
                'description' => 'A one-and-a-half rotation (540 degrees) turning toward the back. Particularly technical because the rider loses sight of their landing during much of the rotation.'
            ],
            [
                'name' => 'Rodeo',
                'description' => 'An off-axis backflip rotation. The rodeo combines a horizontal rotation with a backflip element, creating a fluid and stylish movement that is highly appreciated in freestyle.'
            ],
            [
                'name' => 'Wildcat',
                'description' => 'A backflip performed on the axis of the board (cartwheel). This trick gives the impression that the rider is doing a cartwheel on the snow, hence its name.'
            ],
            [
                'name' => 'Tamedog',
                'description' => 'A frontflip performed on the axis of the board. It\'s the opposite of the wildcat and gives the impression that the rider is diving forward to do a somersault.'
            ],
            [
                'name' => 'Frontside 720',
                'description' => 'A double complete rotation (720 degrees) turning toward the front. This advanced trick requires good mastery of rotations and plenty of air.'
            ],
            [
                'name' => 'Backside 720',
                'description' => 'A double complete rotation (720 degrees) turning toward the back. One of the most technical tricks due to the prolonged loss of visibility during rotation.'
            ],
            [
                'name' => 'McTwist',
                'description' => 'A halfpipe trick combining a frontside 540 with an inversion element. Popularized by Tom Sims, it\'s one of the iconic tricks in halfpipe snowboarding.'
            ],
            [
                'name' => 'Switch Riding',
                'description' => 'A technique where the rider descends with their reverse orientation (right foot forward for a regular, left foot forward for a goofy). Mastering switch is essential for progressing in freestyle.'
            ],
            [
                'name' => 'Cab (Switch Frontside)',
                'description' => 'A frontside rotation performed in switch position (named after Steve Caballero). Cab rotations are particularly appreciated for their technical difficulty.'
            ],
            [
                'name' => 'Frontside Bluntslide',
                'description' => 'A variation of the boardslide where the rider places the back of the board on the rail or box, with the nose hanging off. Advanced rail trick that requires precision and balance.'
            ],
            [
                'name' => 'Backside Bluntslide',
                'description' => 'Same principle as the frontside bluntslide but approaching the rail from the backside. Even more technical because the rider must pivot more to position themselves.'
            ],
            [
                'name' => 'Frontside Noseslide',
                'description' => 'A rail trick where the rider places the front of the board on the rail or box, with the tail hanging off. The inverse position of the bluntslide, equally technical.'
            ],
        ];

        foreach ($snowtrickData as $iValue) {
			$slugger = new AsciiSlugger();

            $snowtrick = new Snowtrick();
            $snowtrick
                ->setCreatedAt(new \DateTimeImmutable('-1 year'))
                ->setCreatedBy($this->users[array_rand($this->users)])
                ->setDescription($iValue['description'])
                ->setName($iValue['name'])
                ->setUpdatedAt($this->faker->boolean(30) ? new \DateTimeImmutable('-1 month') : null)
	            ->setCategory($this->faker->randomElement(SnowtrickCategories::cases()))
	            ->setslug($slugger->slug($iValue['name'])->lower()->toString())
            ;
            $manager->persist($snowtrick);
            $this->snowtricks[] = $snowtrick;
        }

        $manager->flush();
	}

	private function createComments(ObjectManager $manager): void
	{
        $commentData = [
            "This trick changed my riding forever! Took me about 2 weeks of practice to get it clean.",
            "I always struggle with the timing on this one. Any tips from more experienced riders?",
            "Finally landed this after trying for months! So stoked!",
            "The video tutorial really helped me understand the proper technique. Thanks for sharing!",
            "This is definitely my go-to trick when I want to impress my friends on the slopes.",
            "Way harder than it looks! I've been snowboarding for 5 years and still can't nail this consistently.",
            "Love how this trick looks when executed properly. So stylish!",
            "Anyone else find this easier to learn on a powder day?",
            "I teach this to my intermediate students and it's always a crowd-pleaser.",
            "Be careful with your weight distribution when attempting this, I learned the hard way with a nasty fall.",
            "This is the perfect trick to practice in the park before hitting bigger features.",
            "I find this much easier on my custom board than my old rental. Equipment matters!",
            "Awesome explanation! I've been doing this wrong for years.",
            "Just came back from Whistler and saw the pros doing this. Mind-blowing to watch!",
            "Tried this yesterday and ended up face-planting. Back to the drawing board lol.",
            "My 11-year-old son picked this up faster than I did. Kids have no fear!",
            "I've been incorporating this into my line and it flows so naturally.",
            "This trick separates the beginners from the intermediates. Classic benchmark trick.",
            "Nothing beats the feeling when you land this perfectly for the first time.",
            "I find using slightly forward stance angles helps with the rotation.",
            "Been riding for 15 years and this is still one of my favorites. Timeless trick.",
            "I filmed myself trying this and realized my form was all wrong. Video feedback is crucial!",
            "Perfect for flat light days when you can't see the terrain well.",
            "This is basically a prerequisite for more advanced park riding.",
            "My instructor showed me a great progression to build up to this trick safely.",
            "Who else learned this by just sending it and hoping for the best?",
            "I use this as a setup move before transitioning into other tricks.",
            "The key is commitment - you can't hesitate halfway through!",
            "I always make sure to wear impact shorts when practicing this one. Safety first!",
            "This trick looks so much better when you add your own style to it.",
            "Best feeling when you stomp this clean in front of the lift line and hear people cheering.",
            "Does anyone else feel like their dominant leg makes a difference in how easy this is to learn?",
            "I've noticed European riders have a slightly different approach to this technique.",
            "Got this dialed in during a week-long camp in Colorado. Best money I ever spent on improving my riding.",
            "This is the perfect trick to practice on a small feature before taking it to something bigger.",
            "I always warm up with this trick before trying anything more technical.",
            "After watching the Olympics, I was inspired to learn this. Three seasons later and I've almost got it!",
            "The first time I landed this was completely by accident, but now I can do it consistently.",
            "This trick is all about the setup - if you don't approach correctly, you'll never make it around.",
            "My shoulders were the key - once I got my upper body rotation dialed, my board followed naturally.",
            "This is my 8-year-old daughter's favorite trick to show off to her friends.",
            "I find this much easier on a twin-tip board than a directional.",
            "The mental block is real with this one! Took me forever to commit.",
            "The satisfaction of riding away clean from this trick is unmatched.",
            "I practice this on a trampoline board during the off-season to keep the muscle memory.",
            "This trick taught me more about board control than any other.",
            "When I'm teaching beginners, this is usually the first trick they get excited about learning.",
            "I used to think this was impossible until I broke it down into smaller steps.",
            "Love incorporating this into different lines through the park. So versatile!",
            "This is the trick that got me hooked on freestyle snowboarding.",
            "Such a confidence booster when you start landing these consistently.",
            "I find it's all about patience - don't rush the rotation.",
            "Does anyone else have a mental block with this one? Any tips to overcome the fear?",
            "This trick is how I judge the quality of a snowpark - if I can do this comfortably, it's well-built.",
            "Learned this trick on my third day ever snowboarding. Some things just click!",
            "My buddy who skis is always jealous that snowboarders can do this so stylishly.",
            "The slow-mo videos really help with understanding the technique. Thanks for posting!",
            "This trick separates the park rats from the weekend warriors for sure.",
            "Just had ACL surgery and can't wait to get back to doing these next season.",
            "I've noticed my technique varies depending on the steepness of the landing. Anyone else?",
            "Perfect trick for impressing non-snowboarders who have no idea how difficult it actually is.",
            "Finally got this dialed in after watching countless YouTube tutorials.",
            "This trick is all about commitment - you have to fully send it or you'll end up in an awkward position.",
            "The proper grab makes this trick look 10 times better.",
            "I use this as my recovery trick when I'm not feeling confident enough for bigger spins.",
            "My coach spotted that I wasn't using my ankles enough - fixed the issue immediately.",
            "Anyone else find this trick is easier when you're not overthinking it?",
            "This is my go-to for the last run of the day when my legs are tired but I still want to have fun.",
            "Started landing these consistently after upgrading to a more flexible board.",
            "I find counting the rotation in my head helps me stay oriented.",
            "This trick feels so different on a powder day versus a groomer day.",
            "Been chasing the perfect execution of this for years. It's all about the details.",
            "I love how creative you can get with the grab variations on this trick.",
            "This is my wife's favorite trick to watch me do. She says it looks 'floaty'.",
            "I used visualization techniques for weeks before I even attempted this on snow.",
            "This trick taught me a lot about patience in progression.",
            "The transition from learning this on a small jump to a medium one was surprisingly smooth.",
            "Who else loves the feeling of catching air with this trick on a bluebird day?",
            "This trick is a great test of your edge control and balance.",
            "I always practice this one whenever I'm trying out a new board to get a feel for it.",
            "After 3 seasons, I'm finally confident enough to try this on bigger features.",
            "This trick looks so much better in person than in videos - the height and style are impressive!",
            "I find this easier on hardpack than in powder, which surprised me.",
            "This is the trick that made me fall in love with park riding.",
            "I use this as a warmup every time before hitting bigger features.",
            "Had my first serious slam trying this, but got back up and kept at it. Worth it in the end!",
            "The progression from flatground to small features to bigger jumps was key for me learning this safely.",
            "This is such a confidence builder when you start landing it consistently.",
            "Funny how some tricks just click immediately and others take forever to learn.",
            "The muscle memory for this trick has stayed with me even after a season off due to injury.",
            "I can't believe I used to think this trick was impossible for me. Just needed the right instruction!",
            "This trick is definitely a fan favorite when you're riding with friends.",
            "I practice the motions for this one at home on my balance board during the off-season.",
            "The feeling when you spot your landing and ride away clean is absolutely priceless.",
            "This trick is like a rite of passage for every serious snowboarder.",
            "I'm still working on adding my own style to this rather than just getting through the motions.",
            "Such a visually impressive trick that actually isn't as difficult as it looks once you understand the mechanics.",
            "I remember being terrified of this trick, and now I do it without even thinking about it!",
            "This trick taught me a lot about body awareness and spatial orientation in the air."
        ];

		$nbCommentsPerSnowtrick = 32;

        foreach ($this->snowtricks as $snowtrick) {
			for ($i = 0; $i < $nbCommentsPerSnowtrick; $i++) {
				$comment = new Comment();
				$comment
					->setCreatedAt(new \DateTimeImmutable('-1 year'))
					->setCreatedBy($this->users[array_rand($this->users)])
					->setSnowtrick($snowtrick)
					->setEditedAt($this->faker->boolean(30) ? new \DateTimeImmutable('-1 month') : null)
					->setContent($commentData[array_rand($commentData)])
				;
				$manager->persist($comment);
			}
        }

        $manager->flush();
	}

	private function createFiles(ObjectManager $manager): void
	{
		$nbFilesPerSnowtrick = 8;

		$videoLinks = [
			'https://www.youtube.com/embed/SQyTWk7OxSI',
			'https://www.youtube.com/embed/_2qetPRLpp4',
			'https://www.youtube.com/embed/7hL8jYPTOhA',
		];

		$images = [
			'Snowtricks_example_1.jpg',
			'Snowtricks_example_2.avif',
			'Snowtricks_example_3.jpg',
		];

		$imagesDir = __DIR__ . '/../../assets/images';

		// Ensure we have images to use
		if (!is_dir($imagesDir)) {
			throw new \RuntimeException('Images directory not found: ' . $imagesDir);
		}

		// Verify that all example images exist
		foreach ($images as $image) {
			if (!file_exists($imagesDir . '/' . $image)) {
				throw new \RuntimeException('Image file not found: ' . $imagesDir . '/' . $image);
			}
		}

		foreach ($this->snowtricks as $snowtrick) {
			foreach ($videoLinks as $videoLink) {
				$file = new File();
				$file->setSnowtrick($snowtrick)
					->setCreatedBy($this->faker->randomElement($this->users))
					->setCreatedAt(new \DateTimeImmutable())
					->setType('video')
					->setPath($videoLink)
				;

				$manager->persist($file);
			}

			foreach ($images as $image) {
				$file = new File();
				$file->setSnowtrick($snowtrick)
					->setCreatedBy($this->faker->randomElement($this->users))
					->setCreatedAt(new \DateTimeImmutable())
					->setType('image')
				;

				// Image - use our predefined images
				$imagePath = $imagesDir . '/' . $image;

				// Create a temporary UploadedFile to use with the FileManager
				$tempFile = sys_get_temp_dir() . '/' . $image;
				copy($imagePath, $tempFile);
				$uploadedFile = new UploadedFile(
					$tempFile,
					$image,
					mime_content_type($imagePath),
					null,
					true
				);

				// Use the FileManager to handle the upload
				$newFilePath = $this->fileManager->uploadFile($uploadedFile, $snowtrick->getId());

				$file->setPath($newFilePath);

				$manager->persist($file);
			}
		}

		$manager->flush();
	}
}
