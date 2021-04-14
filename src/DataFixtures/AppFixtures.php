<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\BlogPost;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'name' => 'Piotr Jura',
            'password' => 'secret123#'
        ],
        [
            'username' => 'john_doe',
            'email' => 'john@blog.com',
            'name' => 'John Doe',
            'password' => 'secret123#'
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob@blog.com',
            'name' => 'Rob Smith',
            'password' => 'secret123#'
        ],
        [
            'username' => 'jenny_rowling',
            'email' => 'jenny@blog.com',
            'name' => 'Jenny Rowling',
            'password' => 'secret123#'
        ]
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
        
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUsers($manager);
        $this->loadBlogPost($manager);
        $this->loadComments($manager);
        
    }

    public function loadBlogPost(ObjectManager $manager) {
        // $user = $this->getReference('user_admin');

        $this->faker = \Faker\Factory::create();

        for ($i=0; $i < 100; $i++) { 
            $blogpost = new BlogPost();
            $blogpost->setTitle($this->faker->realText(10));
            $blogpost->setPublished($this->faker->dateTimeThisYear());
            $blogpost->setContent($this->faker->realText);

            $authorReference = $this->getRandomUserReference();

            // $blogpost->setAuthor($authorReference);
            $blogpost->setAuthor($authorReference);
            $blogpost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogpost);

            $manager->persist($blogpost);
        }
        
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager) {
        for ($i=0; $i < 100; $i++) { 
            for ($j=0; $j < rand(1,10); $j++) { 
                $comment = new Comment();
                $comment->setContent($this->faker->realText);
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUserReference();

                // $comment->setAuthor($this->getReference('user_admin'));
                // $comment->setAuthor($authorReference);
                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager) {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            // $this->addReference('user_admin', $user);
            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
            $manager->flush();
        }
        
    }

    /**
     * @return User
     */
    protected function getRandomUserReference(): User
    {
        return  $this->getReference('user_'.self::USERS[rand(0, 3)]['username']);
    }
}
