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
    public function login(AuthenticationUtils $authenticationUtils, CsrfTokenManagerInterface $csrfTokenManager,
    Request $request  ): Response
    {
        // Débogage : Vérifier le jeton CSRF et les données POST
        $token = $csrfTokenManager->getToken('authenticate')->getValue();
        dump($token, $request->request->all(), $request->getSession()->all());

        // Si l'utilisateur est déjà connecté, redirige-le vers le tableau de bord approprié en fonction de son rôle
        if ($this->getUser()) {
            // Si l'utilisateur a le rôle "ROLE_ADMIN"
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_home'); // Redirection vers le tableau de bord admin
            } 
            // Si l'utilisateur a le rôle "ROLE_EMPLOYER"
            elseif ($this->isGranted('ROLE_EMPLOYER')) {
                return $this->redirectToRoute('employe_home_index'); // Redirection vers le tableau de bord employé
            }
            // Si aucun rôle défini, redirige vers la page d'accueil (par défaut)
            return $this->redirectToRoute('app_home');
        }
        

        // Récupérer l'erreur de connexion si il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupérer le dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();
        $loginType = $request->getSession()->get('loginType', 'user');
        // Créer le formulaire
        $form = $this->createForm(LoginType::class, null, [
            'loginType' => $loginType,
            'csrf_token_id' => 'authenticate',
             
        ]);

        // Affichage du formulaire de connexion
        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $token,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être vide, elle sera interceptée par la clé "logout" dans la configuration de ton firewall.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
