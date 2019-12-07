<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipRepository")
 */
class Ship
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
    private $base_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShipPlayer", mappedBy="ship")
     */
    private $shipPlayers;

    public function __construct()
    {
        $this->shipPlayers = new ArrayCollection();
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
     * @return Collection|ShipPlayer[]
     */
    public function getShipPlayers(): Collection
    {
        return $this->shipPlayers;
    }

    public function addShipPlayer(ShipPlayer $shipPlayer): self
    {
        if (!$this->shipPlayers->contains($shipPlayer)) {
            $this->shipPlayers[] = $shipPlayer;
            $shipPlayer->setShip($this);
        }

        return $this;
    }

    public function removeShipPlayer(ShipPlayer $shipPlayer): self
    {
        if ($this->shipPlayers->contains($shipPlayer)) {
            $this->shipPlayers->removeElement($shipPlayer);
            // set the owning side to null (unless already changed)
            if ($shipPlayer->getShip() === $this) {
                $shipPlayer->setShip(null);
            }
        }

        return $this;
    }
}
