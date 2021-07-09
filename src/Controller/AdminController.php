<?php
namespace App\Controller;

use App\Entity\Discipline;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class AdminController extends AbstractController {

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $utils
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @Route("/admin/login", name="login")
     */
    public function showConnexion(Request $request, AuthenticationUtils $utils) :Response {

        if(false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $error = $utils->getLastAuthenticationError();
            $lastUsername = $utils->getLastUsername();

            return new Response($this->twig->render('security/login.html.twig', [
                'error' => $error,
                'last_username' => $lastUsername
            ]));
        }
        return new RedirectResponse('dashboard');
    }

    public function showDashboard(Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator) :Response {
        $comingSoon = $em->getRepository(Discipline::class)->statDiscipline(">");
        $inProgress = $em->getRepository(Discipline::class)->statDiscipline("<=");
        $finished = $em->getRepository(Discipline::class)->statDiscipline("", 1);

        $disciplines = $em->getRepository(Discipline::class)->comingSoonDisciplines();

        return new Response($this->twig->render('admin/pages/dashboard.html.twig', [
            'comingSoon' => $comingSoon,
            'inProgress' => $inProgress,
            'finished' => $finished,
            'disciplines' => $disciplines
        ]));
    }

    public function urlRedirectAction(Request $request){
        if(true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return new RedirectResponse('admin/dashboard');
        }else{
            return new RedirectResponse('admin/login');
        }

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}