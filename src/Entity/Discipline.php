<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineRepository")
 */
class Discipline
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
    private $d_game;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $d_format;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $d_organisateur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $d_start_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $d_pts_winner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $d_pts_loser;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $d_default;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $d_coeff;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $d_banner;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $d_finished;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $d_description;

    /**
     * @ORM\ManyToMany(targetEntity="Player", cascade={"persist"})
     * @ORM\JoinTable(name="participant")
     * @return int|null
     */
    private $players;

    /**
     * @ORM\ManyToMany(targetEntity="Team", cascade={"persist"})
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Leaderboard", mappedBy="disciplines")
     * @ORM\OrderBy({"l_position" = "ASC"})
     */
    private $leaderboards;

    /**
     * @ORM\Column(type="boolean", options={"default": false}, nullable=true)
     */
    private $d_streamed = false;


    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->leaderboards = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?string
    {
        return $this->d_game;
    }

    public function setGame(string $d_game): self
    {
        $this->d_game = $d_game;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->d_format;
    }

    public function setFormat(string $d_format): self
    {
        $this->d_format = $d_format;

        return $this;
    }

    public function getOrganisateur(): ?string
    {
        return $this->d_organisateur;
    }

    public function setOrganisateur(string $d_organisateur): self
    {
        $this->d_organisateur = $d_organisateur;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->d_start_date;
    }

    public function setStartDate(?string $d_start_date): self
    {
        $this->d_start_date = $d_start_date;

        return $this;
    }

    public function getPtsWinner(): ?int
    {
        return $this->d_pts_winner;
    }

    public function setPtsWinner(?int $d_pts_winner): self
    {
        $this->d_pts_winner = $d_pts_winner;

        return $this;
    }

    public function getPtsLoser(): ?int
    {
        return $this->d_pts_loser;
    }

    public function setPtsLoser(?int $d_pts_loser): self
    {
        $this->d_pts_loser = $d_pts_loser;

        return $this;
    }

    public function getDefault(): ?int
    {
        return $this->d_default;
    }

    public function setDefault(?int $d_default): self
    {
        $this->d_default = $d_default;

        return $this;
    }

    public function getCoeff(): ?float
    {
        return $this->d_coeff;
    }

    public function setCoeff(?float $d_coeff): self
    {
        $this->d_coeff = $d_coeff;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->d_banner;
    }

    public function setBanner(string $d_banner): self
    {
        $this->d_banner = $d_banner;

        return $this;
    }

    public function getFinished(): ?int
    {
        return $this->d_finished;
    }

    public function setFinished(?int $d_finished): self
    {
        $this->d_finished = $d_finished;

        return $this;
    }

    public function getPlayers()
    {
        return $this->players;
    }

    public function addPlayers(Player $player)
    {
        $this->players[] = $player;
    }

    public function removePlayers(Player $player)
    {
        $this->players->removeElement($player);
    }


    public function getTeams()
    {
        return $this->teams;
    }

    public function addTeams(Team $team)
    {
        $this->teams[] = $team;
    }

    public function removeTeams(Team $team)
    {
        $this->teams->removeElement($team);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->d_description;
    }

    /**
     * @param mixed $d_description
     */
    public function setDescription($d_description): void
    {
        $this->d_description = $d_description;
    }

    public function getLeaderboard(): ?Leaderboard
    {
        return $this->leaderboard;
    }

    public function setLeaderboard(?Leaderboard $leaderboard): self
    {
        $this->leaderboard = $leaderboard;

        return $this;
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
            $leaderboard->setDisciplines($this);
        }

        return $this;
    }

    public function removeLeaderboard(Leaderboard $leaderboard): self
    {
        if ($this->leaderboards->contains($leaderboard)) {
            $this->leaderboards->removeElement($leaderboard);
            // set the owning side to null (unless already changed)
            if ($leaderboard->getDisciplines() === $this) {
                $leaderboard->setDisciplines(null);
            }
        }

        return $this;
    }

    public function getStreamed(): ?bool
    {
        return $this->d_streamed;
    }

    public function setStreamed(bool $d_streamed): self
    {
        $this->d_streamed = $d_streamed;

        return $this;
    }


}
