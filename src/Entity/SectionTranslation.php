<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SectionTranslation
 *
 * @ORM\Table(name="section_translation", indexes={@ORM\Index(name="section_translation_fk0", columns={"section_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\SectionTranslationRepository")
 */
class SectionTranslation
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
     * @ORM\Column(name="lang_id", type="bigint", nullable=false)
     */
    private $langId;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * })
     */
    private $section;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLangId(): ?string
    {
        return $this->langId;
    }

    public function setLangId(string $langId): self
    {
        $this->langId = $langId;

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


}
