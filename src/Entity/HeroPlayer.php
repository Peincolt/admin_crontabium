<?php

namespace App\Entity;

use App\Entity\Traits\UnitTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HeroPlayerAbility", mappedBy="heroPlayer", orphanRemoval=true)
     */
    private $abilites;

    public function __construct()
    {
        $this->abilites = new ArrayCollection();
    }

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

    /**
     * @return Collection|HeroPlayerAbility[]
     */
    public function getabilites(): Collection
    {
        return $this->abilites;
    }

    public function addAbility(HeroPlayerAbility $ability): self
    {
        if (!$this->abilites->contains($ability)) {
            $this->abilites[] = $ability;
            $ability->setHeroPlayer($this);
        }

        return $this;
    }

    public function removeabilites(HeroPlayerAbility $ability): self
    {
        if ($this->abilites->contains($ability)) {
            $this->abilites->removeElement($ability);
            // set the owning side to null (unless already changed)
            if ($ability->getHeroPlayer() === $this) {
                $ability->setHeroPlayer(null);
            }
        }

        return $this;
    }
}
