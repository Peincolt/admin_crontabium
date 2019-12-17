<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuildRepository")
 */
class Guild
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $members;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $id_swgoh;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $galactical_puissance;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="guild")
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="guild_id")
     */
    private $users;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMembers(): ?int
    {
        return $this->members;
    }

    public function setMembers(int $members): self
    {
        $this->members = $members;

        return $this;
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

    public function getGalacticalPuissance(): ?string
    {
        return $this->galactical_puissance;
    }

    public function setGalacticalPuissance(string $galactical_puissance): self
    {
        $this->galactical_puissance = $galactical_puissance;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setGuild($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getGuild() === $this) {
                $player->setGuild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setGuildId($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getGuildId() === $this) {
                $user->setGuildId(null);
            }
        }

        return $this;
    }
}
