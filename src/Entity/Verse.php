<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Verse
 *
 * @ORM\Table(name="verse", indexes={@ORM\Index(name="verse_fk0", columns={"chapter_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\VerseRepository")
 */
class Verse
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
     * @ORM\Column(name="number", type="string", length=16,nullable=false)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var Chapter
     *
     * @ORM\ManyToOne(targetEntity="Chapter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chapter_id", referencedColumnName="id")
     * })
     */
    private $chapter;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Verse
     */
    public function setNumber(string $number): Verse
    {
        $this->number = $number;
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

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): self
    {
        $this->chapter = $chapter;

        return $this;
    }


}
