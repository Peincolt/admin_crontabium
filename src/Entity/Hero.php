<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\InformationTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeroRepository")
 */
class Hero
{
    use InformationTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $base_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HeroPlayer", mappedBy="hero")
     */
    private $heroPlayers;

    /**
     * @ORM\Column(type="json")
     */
    private $categories = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Squad", mappedBy="Hero")
     */
    private $squads;

    /**
     * @ORM\OneToMany(targetEntity=SquadUnit::class, mappedBy="hero")
     */
    private $squadUnits;

    public function __construct()
    {
        $this->heroPlayers = new ArrayCollection();
        $this->squads = new ArrayCollection();
        $this->squadUnits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseId(): ?string
    {
        return $this->base_id;
    }

    public function setBaseId(string $base_id): self
    {
        $this->base_id = $base_id;

        return $this;
    }

    /**
     * @return Collection|HeroPlayer[]
     */
    public function getHeroPlayers(): Collection
    {
        return $this->heroPlayers;
    }

    public function addHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if (!$this->heroPlayers->contains($heroPlayer)) {
            $this->heroPlayers[] = $heroPlayer;
            $heroPlayer->setHero($this);
        }

        return $this;
    }

    public function removeHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if ($this->heroPlayers->contains($heroPlayer)) {
            $this->heroPlayers->removeElement($heroPlayer);
            // set the owning side to null (unless already changed)
            if ($heroPlayer->getHero() === $this) {
                $heroPlayer->setHero(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection|Squad[]
     */
    public function getSquads(): Collection
    {
        return $this->squads;
    }

    public function addSquad(Squad $squad): self
    {
        if (!$this->squads->contains($squad)) {
            $this->squads[] = $squad;
            $squad->addHero($this);
        }

        return $this;
    }

    public function removeSquad(Squad $squad): self
    {
        if ($this->squads->contains($squad)) {
            $this->squads->removeElement($squad);
            $squad->removeHero($this);
        }

        return $this;
    }

    /**
     * @return Collection|SquadUnit[]
     */
    public function getSquadUnits(): Collection
    {
        return $this->squadUnits;
    }

    public function addSquadUnit(SquadUnit $squadUnit): self
    {
        if (!$this->squadUnits->contains($squadUnit)) {
            $this->squadUnits[] = $squadUnit;
            $squadUnit->setHero($this);
        }

        return $this;
    }

    public function removeSquadUnit(SquadUnit $squadUnit): self
    {
        if ($this->squadUnits->removeElement($squadUnit)) {
            // set the owning side to null (unless already changed)
            if ($squadUnit->getHero() === $this) {
                $squadUnit->setHero(null);
            }
        }

        return $this;
    }
}
