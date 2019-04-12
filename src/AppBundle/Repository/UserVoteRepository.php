<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Question;
class UserVoteRepository extends EntityRepository
{
    public function findIfUserAlreadyVotedForThisQuestion(User $user, Question $question)
    {
        $result = $this->getEntityManager()
            ->getRepository('AppBundle:UserVote')
            ->createQueryBuilder('q')
            ->where('q.question = :question')
            ->andWhere('q.user = :user')
            ->setParameter('user', $user)
            ->setParameter('question', $question)
            ->getQuery()
            ->getResult();

        return $result;
    }

}