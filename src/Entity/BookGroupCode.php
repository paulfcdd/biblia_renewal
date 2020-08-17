<?php

namespace App\Entity;

use App\Repository\BookGroupCodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookGroupCodeRepository::class)
 */
class BookGroupCode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=BookGroup::class, inversedBy="code", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookGroup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookGroup(): ?BookGroup
    {
        return $this->bookGroup;
    }

    public function setBookGroup(BookGroup $bookGroup): self
    {
        $this->bookGroup = $bookGroup;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
