<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SquadRepository")
 */
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

    public function __construct()
    {
        $this->Hero = new ArrayCollection();
        $this->Ship = new ArrayCollection();
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
}
