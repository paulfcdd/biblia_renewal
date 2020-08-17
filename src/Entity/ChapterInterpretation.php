<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChapterInterpretation
 *
 * @ORM\Table(name="chapter_interpretation", indexes={@ORM\Index(name="chapter_interpretation_fk0", columns={"interpretation_id"}), @ORM\Index(name="chapter_interpretation_fk1", columns={"chapter_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ChapterInterpretationRepository")
 */
class ChapterInterpretation
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
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var Interpretation
     *
     * @ORM\ManyToOne(targetEntity="Interpretation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="interpretation_id", referencedColumnName="id")
     * })
     */
    private $interpretation;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getInterpretation(): ?Interpretation
    {
        return $this->interpretation;
    }

    public function setInterpretation(?Interpretation $interpretation): self
    {
        $this->interpretation = $interpretation;

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
