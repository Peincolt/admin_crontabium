<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
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
    private $id_swgoh;

    /**
     * @ORM\Column(type="integer")
     */
    private $ally_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $galactical_puissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $characters_galactical_puissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ships_galactical_puissance;

    /**
     * @ORM\Column(type="integer")
     */
    private $gear_given;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CharacterPlayer", mappedBy="player")
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShipPlayer", mappedBy="player")
     */
    private $ships;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->ships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSwgoh(): ?string
    {
        return $this->id_swgoh;
    }

    public function setIdSwgoh(string $id_swgoh): self
    {
        $this->id_swgoh = $id_swgoh;

        return $this;
    }

    public function getAllyCode(): ?int
    {
        return $this->ally_code;
    }

    public function setAllyCode(int $ally_code): self
    {
        $this->ally_code = $ally_code;

        return $this;
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getGalacticalPuissance(): ?string
    {
        return $this->galactical_puissance;
    }

    public function setGalacticalPuissance(string $galactical_puissance): self
    {
        $this->galactical_puissance = $galactical_puissance;

        return $this;
    }

    public function getCharactersGalacticalPuissance(): ?string
    {
        return $this->characters_galactical_puissance;
    }

    public function setCharactersGalacticalPuissance(string $characters_galactical_puissance): self
    {
        $this->characters_galactical_puissance = $characters_galactical_puissance;

        return $this;
    }

    public function getShipsGalacticalPuissance(): ?string
    {
        return $this->ships_galactical_puissance;
    }

    public function setShipsGalacticalPuissance(string $ships_galactical_puissance): self
    {
        $this->ships_galactical_puissance = $ships_galactical_puissance;

        return $this;
    }

    public function getGearGiven(): ?int
    {
        return $this->gear_given;
    }

    public function setGearGiven(int $gear_given): self
    {
        $this->gear_given = $gear_given;

        return $this;
    }

    /**
     * @return Collection|CharacterPlayer[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(CharacterPlayer $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setPlayer($this);
        }

        return $this;
    }

    public function removeCharacter(CharacterPlayer $character): self
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
            // set the owning side to null (unless already changed)
            if ($character->getPlayer() === $this) {
                $character->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ShipPlayer[]
     */
    public function getShips(): Collection
    {
        return $this->ships;
    }

    public function addShip(ShipPlayer $ship): self
    {
        if (!$this->ships->contains($ship)) {
            $this->ships[] = $ship;
            $ship->setPlayer($this);
        }

        return $this;
    }

    public function removeShip(ShipPlayer $ship): self
    {
        if ($this->ships->contains($ship)) {
            $this->ships->removeElement($ship);
            // set the owning side to null (unless already changed)
            if ($ship->getPlayer() === $this) {
                $ship->setPlayer(null);
            }
        }

        return $this;
    }
}
