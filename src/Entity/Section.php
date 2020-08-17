<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 */
class Section
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
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=BookGroup::class, mappedBy="section", orphanRemoval=true)
     */
    private $bookGroups;

    public function __toString()
    {
        return $this->getTitle();
    }

    public function __construct()
    {
        $this->bookGroups = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|BookGroup[]
     */
    public function getBookGroups(): Collection
    {
        return $this->bookGroups;
    }

    public function addBookGroup(BookGroup $bookGroup): self
    {
        if (!$this->bookGroups->contains($bookGroup)) {
            $this->bookGroups[] = $bookGroup;
            $bookGroup->setSection($this);
        }

        return $this;
    }

    public function removeBookGroup(BookGroup $bookGroup): self
    {
        if ($this->bookGroups->contains($bookGroup)) {
            $this->bookGroups->removeElement($bookGroup);
            // set the owning side to null (unless already changed)
            if ($bookGroup->getSection() === $this) {
                $bookGroup->setSection(null);
            }
        }

        return $this;
    }


}
