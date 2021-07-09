<?php

namespace App\Repository;

use App\Entity\Discipline;
use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    public function lastThreeDiscipline(){
        return $this->createQueryBuilder('d')
            ->where("d.d_finished = 0")
            ->orderBy("d.d_start_date", "ASC")
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function comingSoonDisciplines(){
        return $this->createQueryBuilder('d')
            ->orderBy("d.d_start_date", "ASC")
            ->where("d.d_finished = 0")
            ->andWhere("d.d_start_date > ". time())
            ->getQuery()
            ->getResult();
    }

    public function everyDisciplines(){
        return $this->createQueryBuilder('d')
            ->orderBy("d.d_finished", "ASC")
            ->addOrderBy('d.d_start_date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function everyDisciplinesExcept(String $name)
    {
        return $this->createQueryBuilder('d')
            ->where("d.d_organisateur != '" . $name . "'")
            ->orderBy("d.d_start_date", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function deleteParticipant($player){
        $qb = $this->everyDisciplines();
        foreach($qb as $d){
            $d->removePlayers($player);
        }
    }


    /**
     * @param String $sign
     * @param int $finished
     * @return mixed
     */
    public function statDiscipline(String $sign="", $finished=0){
        if($finished == 0) {
            try {
                $qb = $this->createQueryBuilder('d')
                    ->select('count(d.id)')
                    ->where("d.d_start_date " . $sign . " " . time())
                    ->andWhere("d.d_finished = 0")
                    ->getQuery()
                    ->getSingleScalarResult();
            } catch (NoResultException $e) {
                $qb = 0;
            } catch (NonUniqueResultException $e) {
                $qb = 0;
            }
        }else{
            try {
                $qb = $this->createQueryBuilder('d')
                    ->select('count(d.id)')
                    ->where("d.d_finished = 1")
                    ->getQuery()
                    ->getSingleScalarResult();
            } catch (NoResultException $e) {
                $qb = 0;
            } catch (NonUniqueResultException $e) {
                $qb = 0;
            }
        }

        return $qb;
    }


    // /**
    //  * @return Discipline[] Returns an array of Discipline objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discipline
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
