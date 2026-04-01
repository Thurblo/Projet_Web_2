<?php
namespace App\Application\Controller;

use App\Domain\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class LoginController
{
    private EntityManager $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    $model = [];
    if ($request->getMethod() === 'POST') {
        $email = $request->getParsedBody()['email'] ?? '';
        $password = $request->getParsedBody()['password'] ?? '';
        
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => $email]);
        
        if ($user == NULL) {   
            $model['error'] = "Email introuvable";
        } else if (password_verify($password, $user->getMotDePasse())) {
            // Authentification réussie
            $_SESSION['user_id'] = $user->getId();
            return $response->withHeader('Location', '/')->withStatus(302);
        } else {
            $model['error'] = "Mot de passe incorrect";
        }
    }
    $view = Twig::fromRequest($request);
    return $view->render($response, 'Bases/Page_Login.html.twig', $model);
}
}