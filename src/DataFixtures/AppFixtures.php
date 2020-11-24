<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;
    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, 'admin');
        $admin
            ->setEmail('admin@example.com')
            ->setFullName('Administrateur')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u = 0; $u < 3; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($admin, 'password');
            $user
                ->setEmail("user$u@example.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $manager->persist($user);
        }


        for ($c = 0; $c < 5; $c++) {
            $category = new Category();
            $category
                ->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(10, 30); $p++) {
                $product = new Product();
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(1000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setShortDescription($faker->paragraph());

                $manager->persist($product);
            }
        }


        $manager->flush();
    }
}
