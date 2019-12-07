<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipPlayerRepository")
 */
class ShipPlayer
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
    private $number_stars;

    /**
     * @ORM\Column(type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $galactical_puissance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="ships")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ship", inversedBy="shipPlayers")
     */
    private $ship;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberStars(): ?string
    {
        return $this->number_stars;
    }

    public function setNumberStars(string $number_stars): self
    {
        $this->number_stars = $number_stars;

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

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): self
    {
        $this->ship = $ship;

        return $this;
    }
}
