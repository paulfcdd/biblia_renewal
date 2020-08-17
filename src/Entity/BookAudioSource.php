<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookAudioSource
 *
 * @ORM\Table(name="book_audio_source", indexes={@ORM\Index(name="book_audio_source_fk0", columns={"book_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookAudioSourceRepository")
 */
class BookAudioSource
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
     * @ORM\Column(name="source", type="string", length=255, nullable=false)
     */
    private $source;

    /**
     * @ORM\OneToOne(targetEntity=Book::class, inversedBy="bookAudioSource", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }
}
