<?php
namespace AppBundle\Security;

use AppBundle\Entity\Answer;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AnswerVoter extends Voter
{

    const EDIT = 'edit';
    const HIDE = 'hide';


    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;

    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT, self::HIDE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Answer) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $answer = $subject;
        $roleAllowed = $this->decisionManager->decide($token, ['ROLE_MODERATOR']);

        switch ($attribute) {

            case self::EDIT:
                if ($this->decisionManager->decide($token, ['ROLE_MODERATOR'])) {
                    return true;
                }
                return $this->canEdit($answer, $user);

            case self::HIDE:
                if ($this->decisionManager->decide($token, ['ROLE_MODERATOR'])) {
                    return true;
                }
                return $this->canHide($answer, $user);

        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canEdit(Answer $answer, User $user)
    {
        return $user === $answer->getUser();
    }
    private function canHide(Answer $answer, User $user)
    {
        return false;
    }


}