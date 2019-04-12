<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserVoteRepository")
 */
class UserVote
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="votes")
     * @ORM\Id
     */
    private $question;

    /**
     * @ORM\Column(name="vote", type="boolean")
     */
    private $vote;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return UserVote
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     * @return UserVote
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param mixed $vote
     * @return UserVote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;
        return $this;
    }


}