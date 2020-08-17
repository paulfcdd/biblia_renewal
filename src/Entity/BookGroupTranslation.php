<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookGroupTranslation
 *
 * @ORM\Table(name="book_group_translation", indexes={@ORM\Index(name="book_group_translation_fk0", columns={"book_group_id"}), @ORM\Index(name="book_group_translation_fk1", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookGroupTranslationRepository")
 */
class BookGroupTranslation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var BookGroup
     *
     * @ORM\ManyToOne(targetEntity="BookGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_group_id", referencedColumnName="id")
     * })
     */
    private $bookGroup;

    /**
     * @var Lang
     *
     * @ORM\ManyToOne(targetEntity="Lang")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBookGroup(): ?BookGroup
    {
        return $this->bookGroup;
    }

    public function setBookGroup(?BookGroup $bookGroup): self
    {
        $this->bookGroup = $bookGroup;

        return $this;
    }

    public function getLang(): ?Lang
    {
        return $this->lang;
    }

    public function setLang(?Lang $lang): self
    {
        $this->lang = $lang;

        return $this;
    }


}
