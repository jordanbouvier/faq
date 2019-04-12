<?php
namespace AppBundle\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $currentPassword = $user->getPassword();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($user->getPassword() != null)
            {
                $encodedPassword = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encodedPassword);
            }
            else
            {
                $user->setPassword($currentPassword);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('is-success', 'Profil édité avec succès');
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('user/profile.html.twig', [
            'form_user' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/questions", name="user_question")
     */
    public function questionAction(EntityManagerInterface $em)
    {

        $questions = $em->getRepository('AppBundle:Question')
            ->findQuestionsByUser($this->getUser());
        return $this->render('user/question.html.twig', [
            'questions' => $questions,
        ]);
    }
    /**
     * @Route("/profile/stats", name="user_stats")
     */
    public function statsAction()
    {
        return $this->render('user/stats.html.twig');
    }
}