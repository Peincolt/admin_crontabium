<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeroPlayerAbilityRepository")
 */
class HeroPlayerAbility
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasZetaLearned;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasOmicronLearned;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeroPlayer", inversedBy="ability")
     * @ORM\JoinColumn(nullable=false)
     */
    private $heroPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ability", inversedBy="heroPlayerAbilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ability;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHasZetaLearned(): ?bool
    {
        return $this->hasZetaLearned;
    }

    public function setHasZetaLearned(bool $hasZetaLearned): self
    {
        $this->hasZetaLearned = $hasZetaLearned;

        return $this;
    }

    public function getHasOmicronLearned(): ?bool
    {
        return $this->hasOmicronLearned;
    }

    public function setHasOmicronLearned(bool $hasOmicronLearned): self
    {
        $this->hasOmicronLearned = $hasOmicronLearned;

        return $this;
    }

    public function getHeroPlayer(): ?HeroPlayer
    {
        return $this->heroPlayer;
    }

    public function setHeroPlayer(?HeroPlayer $heroPlayer): self
    {
        $this->heroPlayer = $heroPlayer;

        return $this;
    }

    public function getAbility(): ?Ability
    {
        return $this->ability;
    }

    public function setAbility(?Ability $ability): self
    {
        $this->ability = $ability;

        return $this;
    }
}
