<?php

namespace App\DataFixtures;

use App\Entity\Ad;
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private  $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        //$slugify = new Slugify();

        // nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for($i = 1; $i <= 10; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99) . '.jpg';

            if ($genre == "male") {
                $picture = $picture . 'men/' . $pictureId;
            } else {
                $picture = $picture . 'women/' . $pictureId;
            }
            // écriture ternaire
            // $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password'); // prend deux paramètres: enity sur lquelle j'opère, et le ot de passe

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription($content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setPassword($hash)
                ->setPictureUrl($picture)
            ;
            $manager->persist($user);
            $users[] = $user;
        }

        // nous gérons les annonces
        for($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            //$slug = $slugify->slugify($title);
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users)-1)]; // nombre aléatoire parmis les users crées

            $ad->setTitle($title)
                //->setSlug($slug)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(30, 250))
                ->setRooms(mt_rand(1, 8))
                ->setAuthor($user)
            ;

            for($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                ;

                $manager->persist($image);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
