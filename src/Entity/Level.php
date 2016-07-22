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
use UserBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intake_level")
 */
class Level
{
    const ASSESSMENT_NONE = 'none';
    const ASSESSMENT_PASS = 'pass';
    const ASSESSMENT_DOUBT = 'doubt';
    const ASSESSMENT_FAIL = 'fail';

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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $failureErrorCount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $doubtErrorCount;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Text", mappedBy="level", cascade={"persist"})
     */
    protected $texts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Extra", mappedBy="level", cascade={"persist"})
     */
    protected $extras;

    /**
     * @var Intake
     *
     * @ORM\ManyToOne(targetEntity="Intake", inversedBy="levels")
     */
    protected $intake;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->texts = new ArrayCollection();
        $this->extras = new ArrayCollection();
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
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the order.
     *
     * @param $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Returns the position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the number of errors for failure.
     *
     * @param $failureErrorCount
     *
     * @return $this
     */
    public function setFailureErrorCount($failureErrorCount)
    {
        $this->failureErrorCount = $failureErrorCount;

        return $this;
    }

    /**
     * Returns the number of errors for failure.
     *
     * @return string
     */
    public function getFailureErrorCount()
    {
        return $this->failureErrorCount;
    }

    /**
     * Sets the number of errors for doubt.
     *
     * @param $doubtErrorCount
     *
     * @return $this
     */
    public function setDoubtErrorCount($doubtErrorCount)
    {
        $this->doubtErrorCount = $doubtErrorCount;

        return $this;
    }

    /**
     * Returns the number of errors for doubt.
     *
     * @return string
     */
    public function getDoubtErrorCount()
    {
        return $this->doubtErrorCount;
    }

    /**
     * Sets the texts.
     *
     * @param Text[] $texts
     */
    public function setTexts($texts)
    {
        $this->texts->clear();

        foreach ($texts as $text) {
            $this->addText($text);
        }
    }

    /**
     * Adds a text.
     *
     * @param Text $text
     *
     * @return $this
     */
    public function addText(Text $text)
    {
        if (!$this->texts->contains($text)) {
            $this->texts->add($text);
            $text->setLevel($this);
        }

        return $this;
    }

    /**
     * Returns the texts.
     *
     * @return Text[]
     */
    public function getTexts()
    {
        return $this->texts->toArray();
    }

    /**
     * Sets the extras.
     *
     * @param Extra[] $extras
     */
    public function setExtras($extras)
    {
        $this->extras->clear();

        foreach ($extras as $extra) {
            $this->addExtra($extra);
        }
    }

    /**
     * Adds an extra.
     *
     * @param Extra $extra
     *
     * @return $this
     */
    public function addExtra(Extra $extra)
    {
        if (!$this->extras->contains($extra)) {
            $this->extras->add($extra);
            $extra->setLevel($this);
        }

        return $this;
    }

    /**
     * Returns the extras.
     *
     * @return Extra[]
     */
    public function getExtras()
    {
        return $this->extras->toArray();
    }

    /**
     * Sets the intake.
     *
     * @param $intake
     *
     * @return $this
     */
    public function setIntake(Intake $intake)
    {
        $this->intake = $intake;

        return $this;
    }

    /**
     * Returns the intake.
     *
     * @return string
     */
    public function getIntake()
    {
        return $this->intake;
    }

    /**
     * Returns the texts completion status.
     *
     * @param User $user
     *
     * @return bool
     */
    public function getTextsCompleted(User $user)
    {
        /** @var Text $text */
        foreach ($this->texts as $text) {
            if (!$text->getCompleted($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the extras completion status.
     *
     * @param User $user
     *
     * @return bool
     */
    public function getExtrasCompleted(User $user)
    {
        /** @var Extra $extra */
        foreach ($this->extras as $extra) {
            if (!$extra->getCompleted($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the error count.
     *
     * @param User $user
     *
     * @return int
     */
    public function getErrorCount(User $user)
    {
        $count = 0;

        /** @var Text $text */
        foreach ($this->texts as $text) {
            $count += $text->getErrorCount($user);
        }

        return $count;
    }

    /**
     * Returns the assessment.
     *
     * @param User $user
     *
     * @return string
     */
    public function getAssessment(User $user)
    {
        if (!$this->getTextsCompleted($user)) {
            return self::ASSESSMENT_NONE;
        }

        $count = $this->getErrorCount($user);

        if ($count < $this->doubtErrorCount) {
            return self::ASSESSMENT_PASS;
        } elseif ($count < $this->failureErrorCount) {
            return self::ASSESSMENT_DOUBT;
        } else {
            return self::ASSESSMENT_FAIL;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
