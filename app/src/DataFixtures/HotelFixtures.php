<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HotelFixtures extends Fixture
{
    /**
     * @var Factory $faker
     */
    protected $faker;

    const HOTEL_FIXTURE_REFERANCE = "hotel_fixture";

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $record = new Hotel();
            $record->setName($this->faker->company);

            $manager->persist($record);

            $this->addReference(self::HOTEL_FIXTURE_REFERANCE . '_' . $i, $record);
        }

        $manager->flush();
    }
}
