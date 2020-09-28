<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Comments;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder = $encoder;

    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        // gestion des roles
        
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // creation d'n utilisateur special avec un role admin

        $adminUser = new User();
        $adminUser->setFirstName('hurpy')
                    ->setLastName('aurelien')
                    ->setEmail('aurelienhurpy@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser,'password'))
                    ->setAvatar('https://randomuser.me/api/portraits/lego/1.jpg')
                    ->addUserRole($adminRole)
                    ;

        $manager->persist($adminUser);

        $slugify = new Slugify();
        $users = [];
        $genres=['male','female'];
        
        // utilisateurs

        for ($i=1; $i<10; $i++){

            $user = new User();
            $genre = $faker->randomElement($genres);
            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1,99).'.jpg';
            $avatar .= ($genre == 'male' ? 'men/' : 'women/').$avatarId;
            $hash = $this->encoder->encodePassword($user,'password');
            
            
            $description = "<p>".join("</p><p>",$faker->paragraphs(5)). "</p>";
            $user ->setFirstname($faker->firstname)
                    ->setLastname($faker->lastname)
                    ->setEmail($faker->email)
                    ->setHash($hash)
                    ->setAvatar($avatar)
                    ;
                
                $manager->persist($user);
                $users[]=$user;

        }

        // annonces

        for($i=1; $i<=16; $i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(320,280,"food");
            $introduction = $faker->sentence(4);
            $content = "<p>".join("</p><p>",$faker->paragraphs(2)). "</p>";
            

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(30,100))
                ;

            $manager->persist($ad);

            for($j=1; $j<=mt_rand(2,5); $j++){

                // on créée une nouvelle instance de l'entité image
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad)
                        ;
                
                // on sauvegarde
                $manager->persist($image);
            }

            // gestion des reservations

            for($k=1; $k<= mt_rand(0,5); $k++){

                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $guest = mt_rand(3,10);
                $time = new \DateTime();
                $amount = $ad->getPrice() * $guest;

                // trouver le booker

                $booker = $users[mt_rand(0,count($users)-1)];
                $comment = $faker->paragraph();

                // configuration de la reservation

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setTime($time)
                        ->setCreatedAt($createdAt)
                        ->setGuest($guest)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ;

                $manager->persist($booking);

                // gestion des commentaires

                if(mt_rand(0,1)){

                    $comment = new Comments();
                    
                    $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1,5))
                    ->setAuthor($booker)
                    ->setAd($ad)
                    ;

                    $manager->persist($comment);
                    
                }
                    
            }
        }
        $manager->flush();
    }
}
