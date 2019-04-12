<?php
namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use AppBundle\Entity\BlogPost;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $em;
    private $encoder;
    private $password;


    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;

    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_edit' => array('savePasswordUser'),
            'easy_admin.pre_update' => array('editUserPassword'),
        );
    }

    public function savePasswordUser(GenericEvent $event)
    {
        $entity = $event->getArgument('request')->attributes->get('easyadmin')['item'];
        if (!($entity instanceof User)) {
            return;
        }
        $this->password = $entity->getPassword();

    }
    public function editUserPassword(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof User)) {
            return;
        }
        if($entity->getPassword() != null)
        {
            $entity->setPassword($this->encoder->encodePassword($entity, $entity->getPassword()));
        }
        else{
            $entity->setPassword($this->password);
        }


        $event['entity'] = $entity;
    }
}