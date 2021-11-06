<?php

namespace App\Entity;

use App\Entity\Traits\UnitTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipPlayerRepository")
 */
class ShipPlayer
{
    use UnitTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="ships", cascade={"remove"})
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
