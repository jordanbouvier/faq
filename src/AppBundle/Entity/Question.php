<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="questions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="submit_date", type="datetime")
     */
    private $submitDate;

    /**
     * @ORM\Column(name="is_solved", type="boolean")
     */
    private $isSolved;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="questions")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     * @ORM\OrderBy({"submitDate" = "ASC" })
     */
    private $answers;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="questions")
     * @ORM\JoinTable(name="question_tag")
     */
    private $tags;

    /**
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(name="view_count", type="integer")
     */
    private $viewCount;

    /**
     * @ORM\OneToMany(targetEntity="UserVote", mappedBy="question", cascade={"persist", "remove"})
     */
    private $votes;

    /**
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;


    public function __construct()
    {
        $this->submitDate = new \DateTime();
        $this->isSolved = false;
        $this->answers = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->rating = 0;
        $this->viewCount = 0;
        $this->isHidden = false;
    }

    /**
     * @return mixed
     */
    public function getisHidden()
    {
        return $this->isHidden;
    }

    /**
     * @param mixed $isHidden
     * @return Question
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;
        return $this;
    }


    public function addVote(UserVote $vote)
    {
        $this->votes[] = $vote;
        return $this;
    }
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return Question
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    public function addAnswer(Answer $answer)
    {
        $answer->setQuestion($this);
        $this->answers[] = $answer;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return Question
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * @param mixed $submitDate
     * @return Question
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getisSolved()
    {
        return $this->isSolved;
    }

    /**
     * @param mixed $isSolved
     * @return Question
     */
    public function setIsSolved($isSolved)
    {
        $this->isSolved = $isSolved;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }



    /**
     * @param mixed $user
     * @return Question
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * @return mixed
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param mixed $viewCount
     * @return Question
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
        return $this;
    }
    public function isPrivate()
    {
        return false;
    }
    public function __toString()
    {
        return $this->getTitle();
    }
    public function incrementRating()
    {
        $this->rating = $this->getRating()+ 1;
        return $this;
    }
    public function decrementRating()
    {
        $this->rating = $this->getRating() - 1;
        return $this;
    }

}