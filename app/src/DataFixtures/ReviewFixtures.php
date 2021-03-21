<?php

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture
{
    /**
     * @var Factory $faker
     */
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10000; $i++) {

            $record = new Review();
            $record->setHotel($this->getReference(HotelFixtures::HOTEL_FIXTURE_REFERANCE . '_' . $this->faker->numberBetween(0, 9)));
            $record->setScore($this->faker->randomFloat(1, 0, 5));
            $record->setComment($this->faker->paragraph(3));
            $record->setCreatedDate($this->faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now', $timezone = null));

            $manager->persist($record);
        }

        $manager->flush();
    }
}
