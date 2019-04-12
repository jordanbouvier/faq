<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="answers")
 * @ORM\Entity
 */
class Answer
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="submit_date", type="datetime")
     */
    private $submitDate;

    /**
     * @ORM\Column(name="is_visible", type="boolean")
     */
    private $isVisible;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="answers")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     */
    private $question;

    /**
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    public function __construct()
    {
        $this->submitDate = new \DateTime();
        $this->isVisible = 1;
        $this->rating = 0;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }

    /**
     * @return mixed
     */
    public function getisVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param mixed $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     */
    public function setQuestion($question)
    {
        $this->question = $question;
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
     * @return Answer
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }
    public function __toString()
    {
        return $this->getContent();
    }


}