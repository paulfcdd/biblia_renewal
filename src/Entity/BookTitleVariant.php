<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookTitleVariant
 *
 * @ORM\Table(name="book_title_variant", indexes={@ORM\Index(name="book_title_variant_fk0", columns={"book_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookTitleVariantRepository")
 */
class BookTitleVariant
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
     * @ORM\Column(name="main_title", type="string", length=255, nullable=false)
     */
    private $mainTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="chapter_title", type="string", length=255, nullable=false)
     */
    private $chapterTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="page_title", type="string", length=255, nullable=false)
     */
    private $pageTitle;

    /**
     * @var Book
     *
     * @ORM\ManyToOne(targetEntity="Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMainTitle(): ?string
    {
        return $this->mainTitle;
    }

    public function setMainTitle(string $mainTitle): self
    {
        $this->mainTitle = $mainTitle;

        return $this;
    }

    public function getChapterTitle(): ?string
    {
        return $this->chapterTitle;
    }

    public function setChapterTitle(string $chapterTitle): self
    {
        $this->chapterTitle = $chapterTitle;

        return $this;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(string $pageTitle): self
    {
        $this->pageTitle = $pageTitle;

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
