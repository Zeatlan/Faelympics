<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Entity\Player;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\SymfonyBundle;

class PlayerController extends AbstractController
{
    /**
     * @Route("/player", name="player")
     */
    public function index(Request $req, EntityManagerInterface $em)
    {
        $player = new Player();
        $player->setPPts(0);
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $player = $form->getData();
            $this->addFlash("Succès", "Le joueur a été ajouté !");

            $em->persist($player);
            $em->flush();
            return $this->redirectToRoute('player_list');

        }

        $players = $em->getRepository(Player::class)->findAll();


        return $this->render('admin/pages/playerlist.html.twig', [
            'new_player' => $form->createView(),
            'controller_name' => 'PlayerController',
            'current_user' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
            'players' => $players
        ]);
    }

    /**
     * @Route("/admin/player/{id}/edit", name="player_edit")
     * @param Request $request
     * @param Player $player
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
        public function edit(Request $request, Player $player, EntityManagerInterface $em)
        {
            $form = $this->createForm(PlayerType::class, $player);
            $form->add('p_pts', NumberType::class, ['label' => 'Points']);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash("Succès", "La discipline a bien été modifiée !");
                $em->persist($player);
                $em->flush();
                return $this->redirectToRoute('player_list');
            }

            return $this->render('admin/pages/player_edit.html.twig', [
                'edit_player' => $form->createView(),
                'player' => $player
            ]);
        }

        public function delete(Request $request, Player $player, EntityManagerInterface $em)
        {
            if(isset($_SESSION)){
                if($this->get('security.token_storage')->getToken()->getUser()->getUsername() == 'Zeatlan'){
                    $em->getRepository(Discipline::class)->deleteParticipant($player);
                    $em->remove($player);
                    $em->flush();
                    return $this->redirectToRoute('player_list');
                }
            }
            return $this->redirectToRoute('player_list');
        }
}
