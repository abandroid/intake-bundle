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
 */
class QuestionPart extends Part
{
    /**
     * @var string
     */
    protected $type = 'question';

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $choices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="part", cascade={"persist"})
     */
    protected $answers;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->choices = [];
        $this->answers = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        parent::setContent($content);

        $this->choices = explode(',', $this->content);
        $this->choices = array_map(array($this, 'simplify'), $this->choices);

        return $this;
    }

    /**
     * Simplifies a choice to allow it to be compared.
     *
     * @param $value
     *
     * @return string
     */
    public function simplify($value)
    {
        $value = preg_replace('#^[^a-z0-9]+#i', '', $value);
        $value = preg_replace('#[^a-z0-9]+$#i', '', $value);
        $value = mb_strtolower($value);

        return $value;
    }

    /**
     * Adds an answer.
     *
     * @param Answer $answer
     *
     * @return $this
     */
    public function addAnswer(Answer $answer)
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setPart($this);
        }

        return $this;
    }

    /**
     * Returns the answers.
     *
     * @return Answer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Returns the answer for the given user.
     *
     * @param User $user
     *
     * @return Answer
     */
    public function getAnswer(User $user)
    {
        /** @var Answer $answer */
        foreach ($this->answers as $answer) {
            if ($answer->getUser() == $user) {
                return $answer;
            }
        }

        return;
    }

    /**
     * Returns the error count for the given user.
     *
     * @param User $user
     *
     * @return int
     */
    public function getErrorCount(User $user)
    {
        $answer = $this->getAnswer($user);

        if (!$answer) {
            return 1;
        }

        $answer = $this->simplify($answer->getContent());

        if (in_array($answer, $this->choices)) {
            return 0;
        }

        return 1;
    }
}
