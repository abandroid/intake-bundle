<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intake")
 */
class Intake
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $emailAddress;

    /**
     * @ORM\OneToMany(targetEntity="Level", mappedBy="intake", cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $levels;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->levels = new ArrayCollection();
    }

    /**
     * Returns the ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the name.
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the email address.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Sets the email address.
     *
     * @param $emailAddress
     *
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the levels.
     *
     * @param Level[] $levels
     */
    public function setLevels($levels)
    {
        $this->levels->clear();

        foreach ($levels as $level) {
            $this->addLevel($level);
        }
    }

    /**
     * Adds a level.
     *
     * @param Level $level
     *
     * @return $this
     */
    public function addLevel(Level $level)
    {
        if (!$this->levels->contains($level)) {
            $this->levels->add($level);
            $level->setIntake($this);
        }

        return $this;
    }

    /**
     * Returns the levels.
     *
     * @return Level[]
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
