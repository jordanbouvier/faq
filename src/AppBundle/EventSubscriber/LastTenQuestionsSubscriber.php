<?php
namespace AppBundle\EventSubscriber;
use AppBundle\Controller\QuestionController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class LastTenQuestionsSubscriber implements EventSubscriberInterface
{
    private $em;
    private $twig;

    private $listActions = [
      'listAction',
      'showAction',
      'questionsListByTagAction',
      'editAction'
    ];

    public function __construct(EntityManagerInterface $em, \Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->em = $em;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
             return;
        }
        if (get_class($controller[0]) ===  'AppBundle\Controller\QuestionController') {

            if(in_array($controller[1], $this->listActions))
            {
                $questions = $this->em->getRepository('AppBundle:Question')->findLastTenQuestions();
                $this->twig->addGlobal('lastTenQuestions', $questions);

            }

        }
    }

    public static function getSubscribedEvents()
    {
        return array(
             KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}