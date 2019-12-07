<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CharacterPlayerRepository")
 */
class CharacterPlayer
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
    private $id_swgoh;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $international_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $number_stars;

    /**
     * @ORM\Column(type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(type="smallint")
     */
    private $gear_level;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $galactical_puissance;

    /**
     * @ORM\Column(type="smallint")
     */
    private $relic_level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="characters")
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSwgoh(): ?string
    {
        return $this->id_swgoh;
    }

    public function setIdSwgoh(string $id_swgoh): self
    {
        $this->id_swgoh = $id_swgoh;

        return $this;
    }

    public function getInternationalName(): ?string
    {
        return $this->international_name;
    }

    public function setInternationalName(string $international_name): self
    {
        $this->international_name = $international_name;

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

    public function getNumberStars(): ?int
    {
        return $this->number_stars;
    }

    public function setNumberStars(int $number_stars): self
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

    public function getGearLevel(): ?int
    {
        return $this->gear_level;
    }

    public function setGearLevel(int $gear_level): self
    {
        $this->gear_level = $gear_level;

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

    public function getRelicLevel(): ?int
    {
        return $this->relic_level;
    }

    public function setRelicLevel(int $relic_level): self
    {
        $this->relic_level = $relic_level;

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
}
