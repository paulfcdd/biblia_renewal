<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookTranslation
 *
 * @ORM\Table(name="book_translation", indexes={@ORM\Index(name="book_translation_fk0", columns={"book_id"}), @ORM\Index(name="book_translation_fk1", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookTranslationRepository")
 */
class BookTranslation
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var Lang
     *
     * @ORM\ManyToOne(targetEntity="Lang")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return BookTranslation
     */
    public function setTitle(string $title): BookTranslation
    {
        $this->title = $title;
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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }


}
