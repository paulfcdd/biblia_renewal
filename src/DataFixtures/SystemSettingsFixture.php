<?php


namespace App\DataFixtures;


use App\Entity\SystemSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SystemSettingsFixture extends Fixture
{
    public const SETTINGS = [
        'default_lang' => 'r',
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::SETTINGS as $key => $value) {
            $systemSettings = new SystemSettings();

            $systemSettings
                ->setName($key)
                ->setValue($value);

            $manager->persist($systemSettings);
        }
        $manager->flush();
    }
}
