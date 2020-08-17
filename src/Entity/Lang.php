<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lang
 *
 * @ORM\Table(name="lang")
 * @ORM\Entity(repositoryClass="App\Repository\LangRepository")
 */
class Lang implements EntityInterface
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
     * @var string
     *
     * @ORM\Column(name="native_title", type="string", length=255, nullable=false)
     */
    private $nativeTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=10, nullable=false)
     */
    private $isoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="url_slug_code", type="string", length=10, nullable=false)
     */
    private $urlSlugCode;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=ChapterTranslation::class, mappedBy="lang")
     */
    private $chapterTranslations;

    /**
     * @ORM\OneToMany(targetEntity=MainPageLabels::class, mappedBy="lang")
     */
    private $mainPageLabels;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked;

    public function __construct()
    {
        $this->chapterTranslations = new ArrayCollection();
        $this->mainPageLabels = new ArrayCollection();
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

    public function getNativeTitle(): ?string
    {
        return $this->nativeTitle;
    }

    public function setNativeTitle(string $nativeTitle): self
    {
        $this->nativeTitle = $nativeTitle;

        return $this;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getUrlSlugCode(): ?string
    {
        return $this->urlSlugCode;
    }

    public function setUrlSlugCode(string $urlSlugCode): self
    {
        $this->urlSlugCode = $urlSlugCode;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|ChapterTranslation[]
     */
    public function getChapterTranslations(): Collection
    {
        return $this->chapterTranslations;
    }

    public function addChapterTranslation(ChapterTranslation $chapterTranslation): self
    {
        if (!$this->chapterTranslations->contains($chapterTranslation)) {
            $this->chapterTranslations[] = $chapterTranslation;
            $chapterTranslation->setLang($this);
        }

        return $this;
    }

    public function removeChapterTranslation(ChapterTranslation $chapterTranslation): self
    {
        if ($this->chapterTranslations->contains($chapterTranslation)) {
            $this->chapterTranslations->removeElement($chapterTranslation);
            // set the owning side to null (unless already changed)
            if ($chapterTranslation->getLang() === $this) {
                $chapterTranslation->setLang(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MainPageLabels[]
     */
    public function getMainPageLabels(): Collection
    {
        return $this->mainPageLabels;
    }

    public function addMainPageLabel(MainPageLabels $mainPageLabel): self
    {
        if (!$this->mainPageLabels->contains($mainPageLabel)) {
            $this->mainPageLabels[] = $mainPageLabel;
            $mainPageLabel->setLang($this);
        }

        return $this;
    }

    public function removeMainPageLabel(MainPageLabels $mainPageLabel): self
    {
        if ($this->mainPageLabels->contains($mainPageLabel)) {
            $this->mainPageLabels->removeElement($mainPageLabel);
            // set the owning side to null (unless already changed)
            if ($mainPageLabel->getLang() === $this) {
                $mainPageLabel->setLang(null);
            }
        }

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }


}
