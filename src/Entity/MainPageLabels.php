<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MainPageLabels
 *
 * @ORM\Table(name="main_page_labels", indexes={@ORM\Index(name="IDX_main_page_labels_lang_id", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MainPageLabelsRepository")
 */
class MainPageLabels
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false, options={"comment"="Код"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false, options={"comment"="Наименование"})
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Lang::class, inversedBy="mainPageLabels")
     */
    private $lang;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return MainPageLabels
     */
    public function setCode(string $code): MainPageLabels
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return MainPageLabels
     */
    public function setTitle(string $title): MainPageLabels
    {
        $this->title = $title;
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
