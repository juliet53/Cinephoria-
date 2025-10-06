<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LoginType;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
public function login(
    AuthenticationUtils $authenticationUtils,
    Request $request
): Response {
    // Si l'utilisateur est déjà connecté, redirection!!!
    if ($this->getUser()) {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_home');
        } elseif ($this->isGranted('ROLE_EMPLOYER')) {
            return $this->redirectToRoute('employe_home_index');
        }
        return $this->redirectToRoute('app_home');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();
    $loginType = $request->getSession()->get('loginType', 'user');

    $form = $this->createForm(LoginType::class, null, [
        'loginType' => $loginType,
        'csrf_token_id' => 'authenticate',
    ]);
    

    return $this->render('security/login.html.twig', [
        'form' => $form->createView(),
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être vide, elle sera interceptée par la clé "logout" dans la configuration de ton firewall.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
