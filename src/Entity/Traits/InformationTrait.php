<?php

namespace App\Entity\Traits;

trait InformationTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_swgoh;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIdSwgoh(): ?int
    {
        return $this->id_swgoh;
    }

    public function setIdSwgoh(int $id_swgoh): self
    {
        $this->id_swgoh = $id_swgoh;

        return $this;
    }
}