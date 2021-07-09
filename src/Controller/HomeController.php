<?php
namespace App\Controller;

use App\Entity\Discipline;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController {

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index(Request $request, EntityManagerInterface $em) :Response {
        $ourDiscipline = $em->getRepository(Discipline::class)->lastThreeDiscipline();

        return new Response($this->twig->render('pages/home.html.twig', [
            'disciplines' => $ourDiscipline
        ]));
    }
}