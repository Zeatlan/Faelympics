<?php
namespace App\Controller;

use App\Entity\Leaderboard;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
class LeaderboardController {

    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var Request
     */
    private $request;

    public function __construct(Environment $twig, EntityManagerInterface $em, PaginatorInterface $pi, RequestStack $request)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->paginator = $pi;
        $this->request = $request->getCurrentRequest();
    }

    public function index() :Response {
        $void = $this->paginator->paginate(
            $this->em->getRepository(Player::class)->allVisible(),
            $this->request->query->getInt('page', 1),
            10
        );

        return new Response($this->twig->render('pages/leaderboard.html.twig', [
            'leaderKZK' => $this->_medalFactionPerPlayer("Kazoku"),
            'leaderVOID' => $this->_medalFactionPerPlayer("Void Travelers"),
            'leaderFUKYU' => $this->_medalFactionPerPlayer("Fukyu Legacy"),
            'countKZK' => $this->_medalPerFaction("Kazoku"),
            'countVOID' => $this->_medalPerFaction("Void Travelers"),
            'countFUKYU' => $this->_medalPerFaction("Fukyu Legacy"),
            'totalKZK' => $this->em->getRepository(Leaderboard::class)->totalPtsFaction('Kazoku'),
            'totalVOID' => $this->em->getRepository(Leaderboard::class)->totalPtsFaction('Void Travelers'),
            'totalFUKYU' => $this->em->getRepository(Leaderboard::class)->totalPtsFaction('Fukyu Legacy'),
            'leaderboard' => $this->_medalFactionPerPlayer(),
            'pagination' => $void
        ]));
    }

    private function _medalFactionPerPlayer($faction=""){
        if($faction != "")
            $void = $this->em->getRepository(Player::class)->findBy(['p_faction' => $faction]);
        else
            $void = $this->paginator->paginate(
                $this->em->getRepository(Player::class)->allVisible(),
                $this->request->query->getInt('page', 1),
                10
            );
        $arr = [];
        foreach($void as $player){
            $p = [];
            $n = [];
            array_push($p, $player->getPPseudo());
            for($i = 1; $i <= 3; $i++){
                $number = $this->em->getRepository(Leaderboard::class)->getMedal($i, $player->getId());
                array_push($n, $number[0][1]);
            }
            $ptsParticipation = $this->em->getRepository(Leaderboard::class)->atleastYouTried($player->getId());
            array_push($n, $ptsParticipation[0][1]);
            array_push($p, $n);
            array_push($p, $player->getPPts());
            $appeared = $this->em->getRepository(Leaderboard::class)->totalDisciplineAppared($player->getId());
            array_push($p, $appeared[0][1]);
            array_push($p, $player->getPFaction());
            array_push($arr, $p);
        }
        usort($arr, function($a, $b){
            $a = ($a[1][0]*7)+($a[1][1]*5)+($a[1][2]*3)+$a[1][3];
            $b = ($b[1][0]*7)+($b[1][1]*5)+($b[1][2]*3)+$b[1][3];
            if ($a==$b) return 0;
            return ($a>$b)?-1:1;
        });

        return $arr;
    }

    private function _medalPerFaction($faction){
        $arr = [];
        for($i = 1; $i <= 3; $i++) {
            $fac = $this->em->getRepository(Leaderboard::class)->medalPerFaction($i, $faction);
            array_push($arr, $fac);
        }

        return $arr;
    }
}