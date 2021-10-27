<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\InformationTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipRepository")
 */
class Ship
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
     * @ORM\OneToMany(targetEntity="App\Entity\ShipPlayer", mappedBy="ship")
     */
    private $shipPlayers;

    /**
     * @ORM\Column(type="json")
     */
    private $categories = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Squad", mappedBy="Ship")
     */
    private $squads;

    public function __construct()
    {
        $this->shipPlayers = new ArrayCollection();
        $this->squads = new ArrayCollection();
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
            $squad->addShip($this);
        }

        return $this;
    }

    public function removeSquad(Squad $squad): self
    {
        if ($this->squads->contains($squad)) {
            $this->squads->removeElement($squad);
            $squad->removeShip($this);
        }

        return $this;
    }
}
