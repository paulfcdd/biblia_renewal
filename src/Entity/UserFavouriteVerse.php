<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserFavouriteVerse
 *
 * @ORM\Table(name="user_favourite_verse", indexes={@ORM\Index(name="user_favourite_verse_fk0", columns={"user_id"}), @ORM\Index(name="user_favourite_verse_fk1", columns={"verse_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserFavouriteVerseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserFavouriteVerse
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
