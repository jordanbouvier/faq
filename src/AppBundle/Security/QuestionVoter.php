<?php
namespace AppBundle\Security;

use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class QuestionVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const SOLVED = 'solved';
    const DELETE = 'delete';
    const COMMENT = 'comment';
    const CREATE = 'create';
    const VOTE = 'vote';
    const HIDE = 'hide';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;

    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::SOLVED, self::DELETE, self::COMMENT, self::CREATE, self::VOTE, self::HIDE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Question) {
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
        $question = $subject;
        $roleAllowed = $this->decisionManager->decide($token, ['ROLE_MODERATOR']);

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($question, $user);
            case self::EDIT:
                if ($this->decisionManager->decide($token, ['ROLE_MODERATOR'])) {
                    return true;
                }
                return $this->canEdit($question, $user);
            case self::SOLVED:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canSolve($question, $user);
            case self::DELETE:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canDelete($question, $user);
            case self::COMMENT:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canComment($question, $user);
            case self::CREATE:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canCreate();
            case self::VOTE:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canVote($question, $user);
            case self::HIDE:
                if ($roleAllowed) {
                    return true;
                }
                return $this->canHide($question, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }
    private function canView(Question $question, User $user)
    {
        if(!$question->getisSolved())
        {
            return false;
        }

        if ($this->canEdit($question, $user)) {
            return true;
        }


        return !$question->isPrivate();
    }

    private function canEdit(Question $question, User $user)
    {

        return $user === $question->getUser();
    }

    private function canDelete(Question $question, User $user)
    {

        return $user === $question->getUser();
    }
    private function canSolve(Question $question, User $user)
    {
        if($question->getisSolved())
        {
            return false;
        }
        return $user === $question->getUser();
    }
    private function canComment(Question $question, User $user)
    {
        if($question->getisSolved())
        {
            return false;
        }
        return true;
    }
    private function canCreate()
    {
        return true;
    }
    private function canVote(Question $question, User $user)
    {

        return true;
    }
    private function canHide(Question $question, User $user)
    {
        return false;
    }

}