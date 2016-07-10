<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intake_text_part")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"text" = "TextPart", "question" = "QuestionPart"})
 */
abstract class Part
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
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @var Text
     *
     * @ORM\ManyToOne(targetEntity="Text", inversedBy="parts")
     */
    protected $text;

    /**
     * @var Text
     *
     * @ORM\ManyToOne(targetEntity="Extra", inversedBy="parts")
     */
    protected $extra;

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
     * Returns the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Sets the text.
     *
     * @param $text
     *
     * @return $this
     */
    public function setText(Text $text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Returns the text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the extra.
     *
     * @param $extra
     *
     * @return $this
     */
    public function setExtra(Extra $extra = null)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Returns the extra.
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
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
}
