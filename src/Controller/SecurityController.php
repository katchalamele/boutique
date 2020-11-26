<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->addFlash('info', 'Si vous ne voulez pas créer d\'utilisateur alors essayez: email: user0@example.com | password: password');
        $this->addFlash('info', 'Pour tester l\'administration essayez: email: admin@example.com | password: admin');
        $form = $this->createForm(LoginType::class, ['email' => $authenticationUtils->getLastUsername()]);
        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User */
            $user = $form->getData();
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte à bien été créé.');
            $this->addFlash('success', 'Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
