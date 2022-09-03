<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AbilityRepository")
 */
class Ability
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $baseId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isZeta;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOmega;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOmicron;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $omicronMode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hero", inversedBy="abilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hero;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HeroPlayerAbility", mappedBy="ability", orphanRemoval=true)
     */
    private $heroPlayerAbilities;

    public function __construct()
    {
        $this->heroPlayerAbilities = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getBaseId(): ?string
    {
        return $this->baseId;
    }

    public function setBaseId(string $baseId): self
    {
        $this->baseId = $baseId;

        return $this;
    }

    public function getIsZeta(): ?bool
    {
        return $this->isZeta;
    }

    public function setIsZeta(bool $isZeta): self
    {
        $this->isZeta = $isZeta;

        return $this;
    }

    public function getIsOmega(): ?bool
    {
        return $this->isOmega;
    }

    public function setIsOmega(bool $isOmega): self
    {
        $this->isOmega = $isOmega;

        return $this;
    }

    public function getIsOmicron(): ?bool
    {
        return $this->isOmicron;
    }

    public function setIsOmicron(bool $isOmicron): self
    {
        $this->isOmicron = $isOmicron;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOmicronMode(): ?int
    {
        return $this->omicronMode;
    }

    public function setOmicronMode(?int $omicronMode): self
    {
        $this->omicronMode = $omicronMode;

        return $this;
    }

    public function getHero(): ?Hero
    {
        return $this->hero;
    }

    public function setHero(?Hero $hero): self
    {
        $this->hero = $hero;

        return $this;
    }

    /**
     * @return Collection|HeroPlayerAbility[]
     */
    public function getHeroPlayerAbilities(): Collection
    {
        return $this->heroPlayerAbilities;
    }

    public function addHeroPlayerAbility(HeroPlayerAbility $heroPlayerAbility): self
    {
        if (!$this->heroPlayerAbilities->contains($heroPlayerAbility)) {
            $this->heroPlayerAbilities[] = $heroPlayerAbility;
            $heroPlayerAbility->setAbility($this);
        }

        return $this;
    }

    public function removeHeroPlayerAbility(HeroPlayerAbility $heroPlayerAbility): self
    {
        if ($this->heroPlayerAbilities->contains($heroPlayerAbility)) {
            $this->heroPlayerAbilities->removeElement($heroPlayerAbility);
            // set the owning side to null (unless already changed)
            if ($heroPlayerAbility->getAbility() === $this) {
                $heroPlayerAbility->setAbility(null);
            }
        }

        return $this;
    }
}
