<?php
namespace App\Controller;

use App\Entity\Discipline;
use App\Entity\Leaderboard;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\User;
use App\Form\DisciplineTeamType;
use App\Form\DisciplineType;
use App\Form\ParticipantType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DisciplineController extends AbstractController {

    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Environment $twig, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->em = $em;
    }

    public function index() :Response {
        $disciplines = $this->em->getRepository(Discipline::class)->everyDisciplines();
        $now = time();

        return new Response($this->twig->render('pages/discipline.html.twig', [
            'disciplines' => $disciplines,
            'now' => $now
        ]));
    }

    /**
     * @Route("/discipline/{id}", name="epreuve")
     * @param Discipline $discipline
     * @return RedirectResponse|Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showEpreuve(Discipline $discipline) :Response {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $discipline->getOrganisateur()]);
        if($discipline->getFormat() == 'FFA' || $discipline->getFormat() == '1v1') {
            $players = $discipline->getLeaderboards();
        }else {
            $players = $this->em->getRepository(Team::class)->findTeam($discipline);
            //$chefTeam = $this->em->getRepository(Player::class)->findOneBy(['p_pseudo' => $players[0]['chief']]);
        }

        return new Response($this->twig->render('pages/epreuve.html.twig', [
            'discipline' => $discipline,
            'organisateur' => $user,
            'players' => $players,
            'maxPlayers' => sizeof($players),
        ]));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/admin/discipline", name="disciplinelist")
     */
    public function new(Request $request) {
        $discipline = new Discipline();
        $discipline->setOrganisateur($this->get('security.token_storage')->getToken()->getUser()->getUsername());

        $form = $this->createForm(DisciplineType::class, $discipline, ['empty_data' => 'new']);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() &&
            (($form->getData()->getPtsWinner() != null && $form->getData()->getPtsLoser()) || $form->getData()->getDefault() != null)){
            $discipline = $form->getData();
            $date = new \DateTime($discipline->getStartDate());
            $discipline->setStartDate((int)$date->getTimestamp());
            $discipline->setFinished(0);
            $this->em->persist($discipline);
            $this->em->flush();
            return $this->redirectToRoute('disciplinelist');
        }

        $ourDiscipline = $this->em->getRepository(Discipline::class)->findBy(
            ['d_organisateur' => $this->get('security.token_storage')->getToken()->getUser()->getUsername()],
            ['d_start_date' => 'ASC']
            );
        $everyDiscipline = $this->em->getRepository(Discipline::class)->everyDisciplinesExcept('Zeatlan');

        return $this->render('admin/pages/disciplinelist.html.twig', [
            'new_discipline' => $form->createView(),
            'current_user' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
            'disciplines' => $ourDiscipline,
            'allDisciplines' => $everyDiscipline
        ]);

    }

    /**
     * @Route("/admin/discipline/{id}/edit", name="discipline_edit")
     * @param Request $request
     * @param Discipline $discipline
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function edit(Request $request, Discipline $discipline) {
        $form = $this->createForm(DisciplineType::class, $discipline, ['empty_data' => $discipline->getFormat()]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if((($form->getData()->getPtsWinner() != null && $form->getData()->getPtsLoser()) || $form->getData()->getDefault() != null)) {
                $this->addFlash("Succès", "La discipline a bien été modifiée !");
                $date = new \DateTime($discipline->getStartDate());
                $discipline->setStartDate((int)$date->getTimestamp());
                $this->em->persist($discipline);
                $this->em->flush();
                return $this->redirectToRoute('discipline_edit', ['id' => $discipline->getId()]);
            }
        }

        return $this->render('admin/pages/discipline_edit.html.twig', [
            'discipline' => $discipline,
            'edit_discipline' => $form->createView(),
            'current_user' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/admin/discipline/{id}/gestion", name="discipline_gestion")
     * @param Request $request
     * @param Discipline $discipline
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function gestion(Request $request, Discipline $discipline)
    {
        if($discipline->getFormat() == 'TeamVSTeam'){
            $team = new Team();
            $form = $this->createForm(DisciplineTeamType::class, $team);
        }else {
            $player = new Player();
            $form = $this->createForm(ParticipantType::class, $player);
        }
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($discipline->getFormat() == 'TeamVSTeam'){
                $team = $form->getData();
                $t_id = $this->em->getRepository(Team::class)->findOneBy(['name' => $team->getName()]);

                if ('save' === $form->getClickedButton()->getName()) {
                    $discipline->addTeams($t_id);
                } elseif ('desinscrire' === $form->getClickedButton()->getName()) {
                    $discipline->removeTeams($t_id);
                }
            }else {
                $player = $form->getData();
                $p_id = $this->em->getRepository(Player::class)->findOneBy(['p_pseudo' => $player->getPPseudo()]);

                if ('save' === $form->getClickedButton()->getName()) {
                    $discipline->addPlayers($p_id);
                } elseif ('desinscrire' === $form->getClickedButton()->getName()) {
                    $discipline->removePlayers($p_id);
                }
            }

            try {
                $this->em->persist($discipline);
                $this->em->flush();
            }catch(UniqueConstraintViolationException $e){
                return $this->redirectToRoute('discipline_gestion', ['id' => $discipline->getId()]);
            }
        }

        if($discipline->getFinished() == 0)
            if($discipline->getFormat() == 'FFA' || $discipline->getFormat() == '1v1')
                $players = $discipline->getPlayers();
            else
                $players = $discipline->getTeams();
        else
            if($discipline->getFormat() == 'FFA' || $discipline->getFormat() == '1v1')
                $players = $discipline->getLeaderboards();
            else
                $players = $this->em->getRepository(Team::class)->findTeam($discipline);

        return $this->render('admin/pages/discipline_gestion.html.twig', [
            'players' => $players,
            'maxPlayers' => sizeof($players),
            'discipline' => $discipline,
            'inscription' => $form->createView(),
        ]);
    }

    public function ajaxRequest(Request $request)
    {
        if($request->request->get('leaderboard')){
            $arr = $request->request->get('leaderboard');
            $did = $request->request->get('did');
            $discipline = $this->em->getRepository(Discipline::class)->findOneBy(['id' => $did]);

            // Count to decrement for counting position
            $count = sizeof($arr);
            for($i = 0; $i < sizeof($arr); $i++){
                if($discipline->getFormat() == 'TeamVSTeam') {
                    $team = $this->em->getRepository(Team::class)->findOneBy(['name' => $arr[$i]['pseudo']]);
                    foreach ($team->getPlayers() as $player) {
                        $this->_iterate($discipline, $i, $player);
                        $pts = ($count*$discipline->getCoeff())+$discipline->getDefault();
                        $player->setPPts($player->getPPts()+$pts);
                    }
                }else{
                    $player = $this->em->getRepository(Player::class)->findOneBy(['p_pseudo' => $arr[$i]['pseudo']]);
                    $this->_iterate($discipline, $i, $player);
                    $pts = ($count*$discipline->getCoeff())+$discipline->getDefault();
                    $player->setPPts($player->getPPts()+$pts);
                }
                $count--;
            }
            $this->em->flush();

            $response = ['output' => 'ok'];
            return new JsonResponse($response);
        }
        return $this->redirectToRoute('discipline_gestion', ['id'=>$request->request->get('did')]);
    }

    private function _iterate($discipline, $i, $player){
        $leader = new Leaderboard();
        $leader->setDisciplines($discipline);
        $leader->setLPosition($i+1);
        $leader->setPlayers($player);
        $discipline->setLeaderboard($leader);
        $discipline->setFinished(1);
        $this->em->persist($discipline);
        $this->em->persist($leader);
    }

}