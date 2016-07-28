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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intake_extra")
 */
class Extra
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Part", mappedBy="extra", cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $parts;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="Level", inversedBy="extras")
     */
    protected $level;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->parts = new ArrayCollection();
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
     * Sets the content.
     *
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $parts = array();

        $contents = preg_split('#(\[.*?\])#', $content, null, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($contents as $key => $content) {
            if ($key % 2 == 0) {
                $part = new TextPart();
                $part->setContent($content);
            } else {
                $part = new QuestionPart();
                $content = trim($content, '[]');
                $part->setContent($content);
            }
            $part->setPosition($key);
            $parts[] = $part;
        }

        $this->setParts($parts);

        return $this;
    }

    /**
     * Returns the content.
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function getContent(UserInterface $user = null)
    {
        $content = array();

        /** @var Part $part */
        foreach ($this->parts as $part) {
            if ($part instanceof QuestionPart) {
                if ($user) {
                    $answerContent = '(-)';
                    $answer = $part->getAnswer($user);
                    if ($answer instanceof Answer && $answer->getContent() !== '') {
                        $answerContent = $answer->getContent();
                    }
                    $content[] = '<strong>'.$answerContent.'</strong>';
                } else {
                    $content[] = '['.$part->getContent().']';
                }
            } else {
                $content[] = $part->getContent();
            }
        }

        return implode(' ', $content);
    }

    /**
     * Sets the level.
     *
     * @param Level $level
     *
     * @return $this
     */
    public function setLevel(Level $level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Returns the level.
     *
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the parts.
     *
     * @param Part[] $parts
     */
    public function setParts($parts)
    {
        /** @var Part $part */
        foreach ($this->parts as $part) {
            $this->parts->removeElement($part);
            $part->setExtra(null);
        }

        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }

    /**
     * Adds a part.
     *
     * @param Part $part
     *
     * @return $this
     */
    public function addPart(Part $part)
    {
        if (!$this->parts->contains($part)) {
            $this->parts->add($part);
            $part->setExtra($this);
        }

        return $this;
    }

    /**
     * Returns the parts.
     *
     * @return Part[]
     */
    public function getParts()
    {
        return $this->parts->toArray();
    }

    /**
     * Returns the completion status.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function getCompleted(UserInterface $user)
    {
        foreach ($this->parts as $part) {
            if (!$part instanceof QuestionPart) {
                continue;
            }
            $answer = $part->getAnswer($user);
            if (!$answer instanceof Answer) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the error count.
     *
     * @param UserInterface $user
     *
     * @return int
     */
    public function getErrorCount(UserInterface $user)
    {
        $count = 0;

        foreach ($this->parts as $part) {
            if (!$part instanceof QuestionPart) {
                continue;
            }
            $count += $part->getErrorCount($user);
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getContent();
    }
}
