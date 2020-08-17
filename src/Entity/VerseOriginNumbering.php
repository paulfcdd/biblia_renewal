<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VerseOriginNumbering
 *
 * @ORM\Table(name="verse_origin_numbering", indexes={@ORM\Index(name="verse_origin_numbering_fk0", columns={"lang_id"}), @ORM\Index(name="verse_origin_numbering_fk1", columns={"verse_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\VerseOriginNumberingRepository")
 */
class VerseOriginNumbering
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
     * @ORM\Column(name="from", type="string", length=255, nullable=false)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", length=255, nullable=false)
     */
    private $to;

    /**
     * @var Lang
     *
     * @ORM\ManyToOne(targetEntity="Lang")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;

    /**
     * @var Verse
     *
     * @ORM\ManyToOne(targetEntity="Verse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="verse_id", referencedColumnName="id")
     * })
     */
    private $verse;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(string $to): self
    {
        $this->to = $to;

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

    public function getVerse(): ?Verse
    {
        return $this->verse;
    }

    public function setVerse(?Verse $verse): self
    {
        $this->verse = $verse;

        return $this;
    }


}
