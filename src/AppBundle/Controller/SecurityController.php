<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $this->addFlash('is-warning', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('homepage');
        }
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $this->addFlash('is-warning', 'Vous êtes déjà inscrit');
            return $this->redirectToRoute('homepage');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'register' => true,
        ]);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $role = $em->getRepository(Role::class)
                ->findByCode('ROLE_USER');
            $user->setRole($role[0]);
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);

    }

}