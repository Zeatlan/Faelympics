<?php

namespace App\Repository;

use App\Entity\Leaderboard;
use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Leaderboard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leaderboard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leaderboard[]    findAll()
 * @method Leaderboard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaderboardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leaderboard::class);
    }

    public function orderByPosition()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.l_position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function totalDisciplineAppared($player){
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.l_position)')
            ->where('l.players = :player')
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    public function getMedal($position, $player){
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.l_position)')
            ->join('l.players', 'p')
            ->Where('l.l_position = :position')
            ->andWhere('l.players = :player')
            ->setParameter('position', $position)
            ->setParameter('player', $player)
            ->orderBy('COUNT(l.l_position)', 'DESC')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function atleastYouTried($player) {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.l_position)')
            ->where('l.l_position > 3')
            ->andWhere('l.players = :player')
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    public function medalPerFaction($position, $faction){
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.l_position)')
            ->join('l.players', 'p')
            ->where('p.p_faction = :faction')
            ->andWhere('l.l_position = :position')
            ->setParameter('position', $position)
            ->setParameter('faction', $faction)
            ->getQuery()
            ->getResult();
    }

    public function totalPtsFaction($faction) {
        $arr = [];
        for($i = 1; $i <= 3; $i++) {
            $pts = $this->createQueryBuilder('l')
                ->select('COUNT(l.l_position)')
                ->join('l.players', 'p')
                ->where('p.p_faction = :faction')
                ->andWhere('l.l_position = :position')
                ->setParameter('faction', $faction)
                ->setParameter('position', $i)
                ->getQuery()
                ->getResult();
            $pts = (int)$pts[0][1];
            array_push($arr, $pts);
        }
        $reste = $this->createQueryBuilder('l')
            ->select('COUNT(l.l_position)')
            ->join('l.players', 'p')
            ->where('p.p_faction = :faction')
            ->andWhere('l.l_position > 3')
            ->setParameter(':faction', $faction)
            ->getQuery()
            ->getResult();
        $reste = (int)$reste[0][1];

        $total = $arr[0]*7 + $arr[1]*5 + $arr[2]*3 + $reste;
        return $total;
    }

    // /**
    //  * @return Leaderboard[] Returns an array of Leaderboard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    SELECT t.name FROM leaderboard as l
left join player as p on l.players_id = p.id
left join team as t on p.id = t.id
where l.disciplines_id = 8
    */

    /*
    public function findOneBySomeField($value): ?Leaderboard
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
