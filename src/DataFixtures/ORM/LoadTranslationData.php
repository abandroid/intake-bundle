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
use Lexik\Bundle\TranslationBundle\Entity\Translation;
use Lexik\Bundle\TranslationBundle\Entity\TransUnit;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Yaml\Yaml;

class LoadTranslationData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /* @var TransUnitManager $transUnitManager */
        $transUnitManager = $this->container->get('lexik_translation.trans_unit.manager');

        $data = Yaml::parse(file_get_contents(__DIR__.'/../../Resources/fixtures/translation_data.yml'));

        foreach ($data['translations'] as $key => $value) {
            /** @var TransUnit $unit */
            $unit = $transUnitManager->create($key, 'messages', true);
            foreach ($this->container->getParameter('locales') as $locale) {
                $translation = new Translation();
                $translation->setLocale($locale);
                $translation->setContent($value);
                $unit->addTranslation($translation);
            }
            $manager->persist($unit);
            $manager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1000;
    }
}
