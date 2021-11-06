<?php

namespace App\Entity;

use App\Entity\Traits\UnitTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeroPlayerRepository")
 */
class HeroPlayer
{
    use UnitTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="characters", cascade={"remove"})
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hero", inversedBy="heroPlayers")
     */
    private $hero;

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

    public function getHero(): ?Hero
    {
        return $this->hero;
    }

    public function setHero(?Hero $hero): self
    {
        $this->hero = $hero;

        return $this;
    }
}
