<?php

namespace App\Entity\Traits;

trait UnitTrait
{
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
     * @ORM\Column(type="integer")
     */
    private $protection;

    /**
     * @ORM\Column(type="integer")
     */
    private $life;

    /**
     * @ORM\Column(type="integer")
     */
    private $speed;


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

    public function getProtection(): ?int
    {
        return $this->protection;
    }

    public function setProtection(int $protection): self
    {
        $this->protection = $protection;

        return $this;
    }

    public function getLife(): ?int
    {
        return $this->life;
    }

    public function setLife(int $life): self
    {
        $this->life = $life;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }
}