<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LeaderboardRepository")
 */
class Leaderboard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $l_position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discipline", inversedBy="leaderboards")
     */
    private $disciplines;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="leaderboards")
     */
    private $players;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLPosition(): ?int
    {
        return $this->l_position;
    }

    public function setLPosition(int $l_position): self
    {
        $this->l_position = $l_position;

        return $this;
    }

    public function getDisciplines(): ?Discipline
    {
        return $this->disciplines;
    }

    public function setDisciplines(?Discipline $disciplines): self
    {
        $this->disciplines = $disciplines;

        return $this;
    }

    public function getPlayers(): ?Player
    {
        return $this->players;
    }

    public function setPlayers(?Player $players): self
    {
        $this->players = $players;

        return $this;
    }
}
