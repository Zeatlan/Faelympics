<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
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
    private $p_pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $p_faction;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $p_tag;

    /**
     * @ORM\Column(type="integer")
     */
    private $p_pts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Leaderboard", mappedBy="players")
     */
    private $leaderboards;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="players")
     */
    private $teams;


    public function __construct()
    {
        $this->leaderboards = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPPseudo(): ?string
    {
        return $this->p_pseudo;
    }

    public function setPPseudo(string $p_pseudo): self
    {
        $this->p_pseudo = $p_pseudo;

        return $this;
    }

    public function getPFaction(): ?string
    {
        return $this->p_faction;
    }

    public function setPFaction(string $p_faction): self
    {
        $this->p_faction = $p_faction;

        return $this;
    }

    public function getPTag(): ?string
    {
        return $this->p_tag;
    }

    public function setPTag(string $p_tag): self
    {
        $this->p_tag = $p_tag;

        return $this;
    }

    public function getPPts(): ?int
    {
        return $this->p_pts;
    }

    public function setPPts(int $p_pts): self
    {
        $this->p_pts = $p_pts;

        return $this;
    }

    public function __toString()
    {
        return $this->p_pseudo;
    }

    /**
     * @return Collection|Leaderboard[]
     */
    public function getLeaderboards(): Collection
    {
        return $this->leaderboards;
    }

    public function addLeaderboard(Leaderboard $leaderboard): self
    {
        if (!$this->leaderboards->contains($leaderboard)) {
            $this->leaderboards[] = $leaderboard;
            $leaderboard->setPlayers($this);
        }

        return $this;
    }

    public function removeLeaderboard(Leaderboard $leaderboard): self
    {
        if ($this->leaderboards->contains($leaderboard)) {
            $this->leaderboards->removeElement($leaderboard);
            // set the owning side to null (unless already changed)
            if ($leaderboard->getPlayers() === $this) {
                $leaderboard->setPlayers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addPlayer($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removePlayer($this);
        }

        return $this;
    }
}
