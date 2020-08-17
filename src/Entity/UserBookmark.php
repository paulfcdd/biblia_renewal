<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserBookmark
 *
 * @ORM\Table(name="user_bookmark", indexes={@ORM\Index(name="user_bookmark_fk0", columns={"user_id"}), @ORM\Index(name="user_bookmark_fk1", columns={"chapter_id"}), @ORM\Index(name="user_bookmark_fk2", columns={"verse_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserBookmarkRepository")
 */
class UserBookmark
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
     * @var Chapter
     *
     * @ORM\ManyToOne(targetEntity="Chapter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chapter_id", referencedColumnName="id")
     * })
     */
    private $chapter;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): self
    {
        $this->chapter = $chapter;

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
