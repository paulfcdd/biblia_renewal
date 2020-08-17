<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="book", indexes={@ORM\Index(name="book_fk0", columns={"book_group_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
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
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=255, nullable=false)
     */
    private $abbreviation;

    /**
     * @var bool
     *
     * @ORM\Column(name="canonical", type="boolean", nullable=false)
     */
    private $canonical;

    /**
     * @var bool
     *
     * @ORM\Column(name="inscription", type="boolean", nullable=false)
     */
    private $inscription;

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
     * @ORM\OneToMany(targetEntity=BookTranslation::class, mappedBy="book", orphanRemoval=true)
     */
    private $translations;

    /**
     * @ORM\OneToOne(targetEntity=BookAudioSource::class, mappedBy="book", cascade={"persist", "remove"})
     */
    private $bookAudioSource;

    /**
     * @ORM\Column(type="text", length=2024, nullable=true)
     */
    private $hint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $firstId;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getCanonical(): ?bool
    {
        return $this->canonical;
    }

    public function setCanonical(bool $canonical): self
    {
        $this->canonical = $canonical;

        return $this;
    }

    public function getInscription(): ?bool
    {
        return $this->inscription;
    }

    public function setInscription(bool $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getBookGroup(): ?BookGroup
    {
        return $this->bookGroup;
    }

    /**
     * @param BookGroup|null $bookGroup
     * @return $this
     */
    public function setBookGroup(?BookGroup $bookGroup): self
    {
        $this->bookGroup = $bookGroup;

        return $this;
    }

    /**
     * @return Collection|BookTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(BookTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setBook($this);
        }

        return $this;
    }

    public function removeTranslation(BookTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getBook() === $this) {
                $translation->setBook(null);
            }
        }

        return $this;
    }

    public function getBookAudioSource(): ?BookAudioSource
    {
        return $this->bookAudioSource;
    }

    public function setBookAudioSource(BookAudioSource $bookAudioSource): self
    {
        $this->bookAudioSource = $bookAudioSource;

        // set the owning side of the relation if necessary
        if ($bookAudioSource->getBook() !== $this) {
            $bookAudioSource->setBook($this);
        }

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setHint(?string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function getFirstId(): ?int
    {
        return $this->firstId;
    }

    public function setFirstId(?int $firstId): self
    {
        $this->firstId = $firstId;

        return $this;
    }


}
