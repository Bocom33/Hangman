<?php

namespace App\DataFixtures;

use App\Entity\Word;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WordFixtures extends Fixture
{
    const WORDS = [
      'requin'  => 'Sous l\'océan',
      'maison'  => 'Sous le toit',
      'pluie'   => 'Sous les nuages',
      'tiroir'  => 'Sous le bureau',
      'monstre' => 'Sous le lit'
    ];
    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::WORDS as $wordToGuess => $indication) {
            $word = new Word();
            $word->setWord($wordToGuess)->setIndication($indication);
            $this->addReference('word_' . $i, $word);
            $manager->persist($word);
            $i++;
        }
        $manager->flush();
    }
}
