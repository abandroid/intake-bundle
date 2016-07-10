<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intake_answer", uniqueConstraints={@ORM\UniqueConstraint(name="part_user", columns={"part_id", "user_id"})})
 */
class Answer
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
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var QuestionPart
     *
     * @ORM\ManyToOne(targetEntity="QuestionPart", inversedBy="answer")
     */
    protected $part;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="answers")
     */
    protected $user;

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
     * Sets the content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = trim($content);

        return $this;
    }

    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the part.
     *
     * @param QuestionPart $part
     *
     * @return $this
     */
    public function setPart(QuestionPart $part)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * Returns the part.
     *
     * @return QuestionPart
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Sets the user.
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Returns the user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
