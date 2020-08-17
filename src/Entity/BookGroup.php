<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BookGroup
 *
 * @ORM\Table(name="book_group", indexes={@ORM\Index(name="book_group_fk0", columns={"section_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookGroupRepository")
 */
class BookGroup
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
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="bookGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="bookGroup", orphanRemoval=true)
     */
    private $books;

    /**
     * @ORM\OneToOne(targetEntity=BookGroupCode::class, mappedBy="bookGroup", cascade={"persist", "remove"})
     */
    private $code;

    public function __construct()
    {
        $this->books = new ArrayCollection();
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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setBookGroup($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getBookGroup() === $this) {
                $book->setBookGroup(null);
            }
        }

        return $this;
    }

    public function getCode(): ?BookGroupCode
    {
        return $this->code;
    }

    public function setCode(BookGroupCode $code): self
    {
        $this->code = $code;

        // set the owning side of the relation if necessary
        if ($code->getBookGroup() !== $this) {
            $code->setBookGroup($this);
        }

        return $this;
    }

}
