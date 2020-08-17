<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VerseTranslation
 *
 * @ORM\Table(name="verse_translation", indexes={@ORM\Index(name="verse_translation_fk0", columns={"verse_id"}), @ORM\Index(name="verse_translation_fk1", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\VerseTranslationRepository")
 */
class VerseTranslation
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
     * @ORM\Column(name="original_translation", type="text", length=0, nullable=false)
     */
    private $originalTranslation;

    /**
     * @var string
     *
     * @ORM\Column(name="prepared_translation", type="text", length=0, nullable=false)
     */
    private $preparedTranslation;

    /**
     * @var Verse
     *
     * @ORM\ManyToOne(targetEntity="Verse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="verse_id", referencedColumnName="id")
     * })
     */
    private $verse;

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

    public function getOriginalTranslation(): ?string
    {
        return $this->originalTranslation;
    }

    public function setOriginalTranslation(string $originalTranslation): self
    {
        $this->originalTranslation = $originalTranslation;

        return $this;
    }

    public function getPreparedTranslation(): ?string
    {
        return $this->preparedTranslation;
    }

    public function setPreparedTranslation(string $preparedTranslation): self
    {
        $this->preparedTranslation = $preparedTranslation;

        return $this;
    }

    public function getVerse(): ?Verse
    {
        return $this->verse;
    }

    public function setVerse(?Verse $verse): self
    {
        $this->verse = $verse;

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
