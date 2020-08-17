<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VerseCrossReference
 *
 * @ORM\Table(name="verse_cross_reference", indexes={@ORM\Index(name="verse_cross_reference_fk0", columns={"source_verse"}), @ORM\Index(name="verse_cross_reference_fk1", columns={"target_verse"})})
 * @ORM\Entity(repositoryClass="App\Repository\VerseCrossReferenceRepository")
 */
class VerseCrossReference
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
     * @ORM\Column(type="string")
     */
    private $sourceVerse;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $targetVerse;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSourceVerse(): string
    {
        return $this->sourceVerse;
    }

    /**
     * @param string $sourceVerse
     * @return VerseCrossReference
     */
    public function setSourceVerse(string $sourceVerse): VerseCrossReference
    {
        $this->sourceVerse = $sourceVerse;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetVerse(): string
    {
        return $this->targetVerse;
    }

    /**
     * @param string $targetVerse
     * @return VerseCrossReference
     */
    public function setTargetVerse(string $targetVerse): VerseCrossReference
    {
        $this->targetVerse = $targetVerse;
        return $this;
    }


}
