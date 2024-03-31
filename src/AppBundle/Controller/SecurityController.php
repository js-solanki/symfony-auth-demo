<?php 
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use AppBundle\Entity\User;

class SecurityController extends Controller
{
    
    
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function loginAction(Request $request)
    {
        // Extract credentials from the request
        $credentials = json_decode($request->getContent(), true);
       
        // Perform authentication (check credentials)
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'email' => $credentials['email']
        ]);
        // return new JsonResponse(['token' => !empty($user) ? 'user':'null']);
        if (!$user || !$this->isPasswordValid($user, $credentials['password'])) {
            return new JsonResponse(['token' => "invalid password"]);
        }

        // Generate JWT token
        $token = $this->jwtManager->create($user);

        // Return token as JSON response
        return new JsonResponse(['token' => $token]);
    }

    private function isPasswordValid($user, $password)
    {
        // Implement password validation logic here
        // You might use Symfony's built-in password encoder
        // or any other method to validate the password
        // For example:
        // return $this->passwordEncoder->isPasswordValid($user, $password);

        // For demonstration purposes, let's assume plain text comparison
        return $user->getPassword() === $password;
    }
}