<?php


namespace App\DataFixtures;


use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SectionFixtures extends Fixture
{
    public const SECTIONS = [
        [
            'title' => 'Новый Завет',
            'code' => 'new_testament',
        ],
        [
            'title' => 'Ветхий Завет',
            'code' => 'old_testament',
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::SECTIONS as $singleSection) {
            $section = new Section();
            $section
                ->setTitle($singleSection['title'])
                ->setCode($singleSection['code']);

            $manager->persist($section);
        }

        $manager->flush();
    }
}
