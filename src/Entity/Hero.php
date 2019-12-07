<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeroRepository")
 */
class Hero
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
     * @ORM\OneToMany(targetEntity="App\Entity\HeroPlayer", mappedBy="hero")
     */
    private $heroPlayers;

    public function __construct()
    {
        $this->heroPlayers = new ArrayCollection();
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
     * @return Collection|HeroPlayer[]
     */
    public function getHeroPlayers(): Collection
    {
        return $this->heroPlayers;
    }

    public function addHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if (!$this->heroPlayers->contains($heroPlayer)) {
            $this->heroPlayers[] = $heroPlayer;
            $heroPlayer->setHero($this);
        }

        return $this;
    }

    public function removeHeroPlayer(HeroPlayer $heroPlayer): self
    {
        if ($this->heroPlayers->contains($heroPlayer)) {
            $this->heroPlayers->removeElement($heroPlayer);
            // set the owning side to null (unless already changed)
            if ($heroPlayer->getHero() === $this) {
                $heroPlayer->setHero(null);
            }
        }

        return $this;
    }
}
