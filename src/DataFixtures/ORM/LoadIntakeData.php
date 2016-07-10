<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Endroid\Bundle\IntakeBundle\Entity\Extra;
use Endroid\Bundle\IntakeBundle\Entity\Intake;
use Endroid\Bundle\IntakeBundle\Entity\Level;
use Endroid\Bundle\IntakeBundle\Entity\Text;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadIntakeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($n = 1; $n <= 3; ++$n) {
            $intake = $this->createIntake($n);
            $manager->persist($intake);
        }

        $manager->flush();
    }

    /**
     * Creates an intake.
     *
     * @param int $n
     *
     * @return Intake
     */
    protected function createIntake($n)
    {
        $intake = new Intake();
        $intake->setName('Intake '.(date('Y') + $n - 1));
        $intake->setEmailAddress('jeroenvandenenden@gmail.com');

        for ($n = 1; $n <= 3; ++$n) {
            $level = $this->createLevel($n);
            $intake->addLevel($level);
        }

        return $intake;
    }

    /**
     * Creates a level.
     *
     * @param int $n
     *
     * @return Level
     */
    protected function createLevel($n)
    {
        $level = new Level();
        $level->setPosition($n);
        $level->setName('Level '.$n);
        $level->setDoubtErrorCount(7);
        $level->setFailureErrorCount(9);

        $faker = Factory::create();

        for ($n = 1; $n <= 3; ++$n) {
            $content = implode(' ', $faker->words(6));
            for ($i = 0; $i < 10; ++$i) {
                $answers = array();
                for ($a = 1; $a <= 3; ++$a) {
                    $answers[] = 'answer'.$a;
                }
                $content .= ' ['.implode(',', $answers).']';
                $content .= ' '.implode(' ', $faker->words(6));
            }
            $text = new Text();
            $text->setTitle(implode(' ', $faker->words(5)));
            $text->setContent($content);
            $level->addText($text);
        }

        for ($n = 1; $n <= 3; ++$n) {
            $content = implode(' ', $faker->words(6)).' [answer]'.implode(' ', $faker->words(6));
            $extra = new Extra();
            $extra->setContent($content);
            $level->addExtra($extra);
        }

        return $level;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1000;
    }
}
