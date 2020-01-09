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
     * @ORM\Column(type="datetime", length=255)
     */
    private $last_updated;

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
     * @ORM\OneToMany(targetEntity="App\Entity\HeroPlayer", mappedBy="player")
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShipPlayer", mappedBy="player")
     */
    private $ships;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Guild", inversedBy="players")
     */
    private $guild;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="players")
     */
    private $teams;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->ships = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeInterface $last_updated): self
    {
        $this->last_updated = $last_updated;

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
     * @return Collection|HeroPlayer[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(HeroPlayer $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setPlayer($this);
        }

        return $this;
    }

    public function removeCharacter(HeroPlayer $character): self
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

    public function getGuild(): ?Guild
    {
        return $this->guild;
    }

    public function setGuild(?Guild $guild): self
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addPlayer($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removePlayer($this);
        }

        return $this;
    }
}
