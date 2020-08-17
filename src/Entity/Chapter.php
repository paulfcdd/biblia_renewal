<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chapter
 *
 * @ORM\Table(name="chapter", indexes={@ORM\Index(name="chapter_fk0", columns={"book_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ChapterRepository")
 */
class Chapter
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
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var Book
     *
     * @ORM\ManyToOne(targetEntity="Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    /**
     * @ORM\OneToMany(targetEntity=ChapterTranslation::class, mappedBy="chapter")
     */
    private $chapterTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ChapterInterpretation", mappedBy="chapter")
     */
    private $interpretations;

    public function __construct()
    {
        $this->chapterTranslations = new ArrayCollection();
        $this->interpretations = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

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

    /**
     * @return Collection|ChapterTranslation[]
     */
    public function getChapterTranslations(): Collection
    {
        return $this->chapterTranslations;
    }

    public function addChapterTranslation(ChapterTranslation $chapterTranslation): self
    {
        if (!$this->chapterTranslations->contains($chapterTranslation)) {
            $this->chapterTranslations[] = $chapterTranslation;
            $chapterTranslation->setChapter($this);
        }

        return $this;
    }

    public function removeChapterTranslation(ChapterTranslation $chapterTranslation): self
    {
        if ($this->chapterTranslations->contains($chapterTranslation)) {
            $this->chapterTranslations->removeElement($chapterTranslation);
            // set the owning side to null (unless already changed)
            if ($chapterTranslation->getChapter() === $this) {
                $chapterTranslation->setChapter(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getInterpretations()
    {
        return $this->interpretations;
    }

    /**
     * @param ChapterInterpretation $interpretation
     * @return $this
     */
    public function addInterpretation(ChapterInterpretation $interpretation)
    {
        if (!$this->interpretations->contains($interpretation)) {
            $this->interpretations->add($interpretation);
            $interpretation->setChapter($this);
        }

        return $this;
    }

    /**
     * @param ChapterInterpretation $interpretation
     * @return $this
     */
    public function removeInterpretation(ChapterInterpretation $interpretation)
    {
        if ($this->interpretations->contains($interpretation)) {
            $this->interpretations->remove($interpretation);
            if ($interpretation->getChapter() === $this) {
                $interpretation->setChapter(null);
            }
        }

        return $this;
    }
}
