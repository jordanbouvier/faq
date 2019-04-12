<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class QuestionRepository extends EntityRepository
{
    public function findAllOrderedByDateDesc()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT q.title, q.id FROM AppBundle:Question q ORDER BY q.submitDate DESC')
            ->getResult();
    }
    public function findLastTenQuestions()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT q FROM AppBundle:Question q ORDER BY q.submitDate DESC')
            ->setMaxResults(10)
            ->getResult();
    }



    public function findAllByPage($currentPage = 1, $limit = 10)
    {
        $countQuestions = $this->getQuestionsCount();
        $pageMax = ceil($countQuestions[0]['1'] / $limit);

        if($currentPage <= 0) { $currentPage = 1; };
        if($currentPage > $pageMax) { $currentPage = $pageMax; };

        $offset = $limit * ($currentPage - 1);
        $query = $this->getEntityManager()
        ->createQuery('SELECT q FROM AppBundle:Question q ORDER BY q.submitDate DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
        return $query;

    }

    public function getQuestionsCount()
    {
        $count = $this->getEntityManager()
            ->createQuery('SELECT count(q.id) FROM AppBundle:Question q')
            ->getResult();

        return $count;
    }


    public function findQuestionsByUser(User $user)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Question')
            ->createQueryBuilder('q')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findQuestionWithTags($tags)
    {
        if(!$tags)
        {
            return false;
        }

        if(!preg_match('/^([a-zA-Z]+\,?){1,}[a-zA-Z]$/', $tags))
        {
            return false;
        }
        $tagList = explode(',', $tags);

        $list = '';
        foreach($tagList as $key => $tag)
        {
            $list.= ':' . $tag . $key;
            if(key_exists($key + 1, $tagList))
            {
                $list.= ' OR qt.name = ';
            }
        }

        $query = $this->getEntityManager()
            ->getRepository('AppBundle:Question', 'q')
            ->createQueryBuilder('s')
            ->innerJoin('s.tags', 'qt')
            ->where('qt.name = '. $list);
        foreach($tagList as $key => $tag)
        {
            $query = $query->setParameter(':' . $tag . $key, $tag);
        }

        $results = $query->getQuery()->getResult();
        return $results;
    }

    public function countNotHiddenQuestion()
    {
        $countQuestions = $query = $this->getEntityManager()
            ->getRepository('AppBundle:Question')
            ->createQueryBuilder('q')
            ->select('COUNT(q)')
            ->where('q.isHidden = false')
            ->getQuery()
            ->getResult();

        return $countQuestions;
    }
    public function findAllVisibleQuestion($currentPage = 1, $limit = 10)
    {
        $countQuestions = $query = $this->countNotHiddenQuestion();

        $pageMax = ceil($countQuestions[0]['1'] / $limit);

        if($currentPage <= 0) { $currentPage = 1; };
        if($currentPage > $pageMax) { $currentPage = $pageMax; };

        $offset = $limit * ($currentPage - 1);
        $query = $this->getEntityManager()
            ->getRepository('AppBundle:Question')
            ->createQueryBuilder('q')
            ->where('q.isHidden = false')
            ->orderBy('q.submitDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $query->getQuery()
            ->getResult();

        return $results;

    }

}