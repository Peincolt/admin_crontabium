<?php

namespace App\Entity;

use App\Repository\SquadUnitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SquadUnitRepository::class)
 */
class SquadUnit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Squad::class, inversedBy="squadUnits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $squad;

    /**
     * @ORM\ManyToOne(targetEntity=Hero::class, inversedBy="squadUnits")
     */
    private $hero;

    /**
     * @ORM\ManyToOne(targetEntity=Ship::class, inversedBy="squadUnits")
     */
    private $ship;

    /**
     * @ORM\Column(type="integer")
     */
    private $showOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSquad(): ?Squad
    {
        return $this->squad;
    }

    public function setSquad(?Squad $squad): self
    {
        $this->squad = $squad;

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

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): self
    {
        $this->ship = $ship;

        return $this;
    }

    public function getShowOrder(): ?int
    {
        return $this->showOrder;
    }

    public function setShowOrder(int $showOrder): self
    {
        $this->showOrder = $showOrder;

        return $this;
    }

    public function getUnitByType(string $type)
    {
        if ($type == "hero") {
            return $this->hero;
        } else {
            return $this->ship;
        }
    }
}
