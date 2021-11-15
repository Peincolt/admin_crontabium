<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SquadRepository")
 * @UniqueEntity(fields="name",message="Une équipe avec le même nom existe déjà") */
class Squad
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Hero", inversedBy="squads")
     */
    private $Hero;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ship", inversedBy="squads")
     */
    private $Ship;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $used;

    /**
     * @ORM\OneToMany(targetEntity=SquadUnit::class, mappedBy="squad", orphanRemoval=true)
     * @ORM\OrderBy({"showOrder" = "ASC"})
     */
    private $squadUnits;

    public function __construct()
    {
        $this->Hero = new ArrayCollection();
        $this->Ship = new ArrayCollection();
        $this->squadUnits = new ArrayCollection();
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

    /**
     * @return Collection|Hero[]
     */
    public function getHero(): Collection
    {
        return $this->Hero;
    }

    public function addHero(Hero $hero): self
    {
        if (!$this->Hero->contains($hero)) {
            $this->Hero[] = $hero;
        }

        return $this;
    }

    public function removeHero(Hero $hero): self
    {
        if ($this->Hero->contains($hero)) {
            $this->Hero->removeElement($hero);
        }

        return $this;
    }

    /**
     * @return Collection|Ship[]
     */
    public function getShip(): Collection
    {
        return $this->Ship;
    }

    public function addShip(Ship $ship): self
    {
        if (!$this->Ship->contains($ship)) {
            $this->Ship[] = $ship;
        }

        return $this;
    }

    public function removeShip(Ship $ship): self
    {
        if ($this->Ship->contains($ship)) {
            $this->Ship->removeElement($ship);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUsed(): ?string
    {
        return $this->used;
    }

    public function setUsed(string $used): self
    {
        $this->used = $used;

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
            $squadUnit->setSquad($this);
        }

        return $this;
    }

    public function removeSquadUnit(SquadUnit $squadUnit): self
    {
        if ($this->squadUnits->removeElement($squadUnit)) {
            // set the owning side to null (unless already changed)
            if ($squadUnit->getSquad() === $this) {
                $squadUnit->setSquad(null);
            }
        }

        return $this;
    }
}
