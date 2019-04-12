<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\UserVote;
use AppBundle\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\AnswerType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class QuestionController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function listAction(Request $request, EntityManagerInterface $em)
    {
        $questionsPerPage = 10;
        $countQuestions = $em->getRepository(Question::class)
            ->countNotHiddenQuestion();
        $questions = $em->getRepository(Question::class)
            ->findAllVisibleQuestion($request->query->get('page', 1), $questionsPerPage);

        return $this->render('main/homepage.html.twig', [
            'questions' => $questions,
            'countQuestions' => (int)$countQuestions[0]['1'],
            'questionsPerPage' => (int)$questionsPerPage,
        ]);

    }

    /**
     * @Route("/question/show/{id}", name="question_show")
     */
    public function showAction(Question $question, Request $request, EntityManagerInterface $em)
    {
        $question->setViewCount($question->getViewCount() + 1);
        $em->persist($question);
        $em->flush();

        if ($this->isGranted('comment', $question)) {
            $answer = new Answer();
            $form = $this->createForm(AnswerType::class, $answer);

        }

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'form_edit' => ($this->isGranted('comment', $question) ? $form->createView() : false),
        ]);
    }

    /**
     * @Route("/question/new", name="question_new")
     */
    public function newQuestion(EntityManagerInterface $em, Request $request)
    {
        $question = new Question();
        $this->denyAccessUnlessGranted('create', $question);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $question->setUser($this->getUser());
            $em->persist($question);
            $em->flush();
            $this->addFlash('is-success', 'Question envoyée avec succès');
            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }
        return $this->render('question/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/question/search/tag", name="question_by_tag")
     */
    public function questionsListByTagAction(Request $request, EntityManagerInterface $em)
    {
        $tags = $request->get('q', false);
        $questions = $em->getRepository('AppBundle:Question')->findQuestionWithTags($tags);

        return $this->render('question/tag_search.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/question/solve", name="question_solve")
     * @Method({"POST"})
     */
    public function solveAction(Request $request, EntityManagerInterface $em)
    {
        $question = $em->getRepository('AppBundle:Question')
            ->findOneById($request->request->get('id'));
        if(!$question)
        {
            throw  $this->createNotFoundException();
        }
        $this->denyAccessUnlessGranted('solved', $question);
        $question->setIsSolved(true);
        $em->persist($question);
        $em->flush();

        return $this->json('true');

    }
    /**
     * @Route("/question/vote", name="question_vote")
     * @Method({"POST"})
     */
    public function voteAction(Request $request, EntityManagerInterface $em)
    {

        $questionRepository = $em->getRepository('AppBundle:Question');
        $question = $questionRepository
            ->findOneById($request->request->get('id'));

        if(!$question)
        {
            throw  $this->createNotFoundException();
        }
        if(!$this->isGranted('vote', $question)) {
            throw new AccessDeniedHttpException();
        }


        $action = $request->request->get('action');
        if($action != 'upvote' && $action != 'downvote')
        {
            throw $this->createNotFoundException();
        }
        $action = $action == "upvote" ? true : false;

        $findVote = $em->getRepository('AppBundle:UserVote')->findIfUserAlreadyVotedForThisQuestion($this->getUser(), $question);
        if(count($findVote) > 0)
        {
           if($findVote[0]->getVote() === $action)
           {
               return new JsonResponse(false);
           }
           $previousVote = $findVote[0]->getVote();
           $previousVote ? $question->decrementRating() : $question->incrementRating();
           $em->remove($findVote[0]);
           $em->flush();
            return new JsonResponse(json_encode([
                'voteCount' => $question->getRating(),
            ]));
        }
        $newVote = new UserVote();
        $newVote->setQuestion($question)
            ->setUser($this->getUser())
            ->setVote($action);

        if($action == 'upvote') { $question->incrementRating();}
        else { $question->decrementRating(); }
        $question->addVote($newVote);
        $em->persist($question);
        $em->flush();

        return new JsonResponse(json_encode([
            'voteCount' => $question->getRating(),
        ]));

    }

    /**
     * @Route("/question/hide-answer", name="hide_answer")
     */
    public function hideAnswerAction(Request $request, EntityManagerInterface $em)
    {
        $answerRepository = $em->getRepository('AppBundle:Answer');
        $answer = $answerRepository
            ->findOneById($request->request->get('id'));

        if(!$answer)
        {
            throw $this->createNotFoundException();
        }
        if(!$this->isGranted('hide', $answer))
        {
           throw $this->createAccessDeniedException();
        }
        $answer->setIsVisible(false);
        $em->persist($answer);
        $em->flush();
        return new JsonResponse(true);
    }
    /**
     * @Route("/question/hide", name="question_hide")
     */
    public function hideQuestion(Request $request, EntityManagerInterface $em)
    {
        $questionRepository = $em->getRepository('AppBundle:Question');
        $question = $questionRepository
            ->findOneById($request->request->get('id'));

        if(!$question)
        {
            throw $this->createNotFoundException();
        }
        if(!$this->isGranted('hide', $question))
        {
           throw $this->createAccessDeniedException();
        }
        $question->setIsHidden(true);
        $em->persist($question);
        $em->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/question/edit/{id}", name="question_edit")
     */
    public function editAction(Question $question, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $question);

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('is-success', 'Question éditée avec succès');
            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }
        return $this->render('question/edit.html.twig', [
            'question_form_edit' => $form->createView(),
            'question' => $question,
        ]);
    }

    /**
     * @Route("/question/add-answer/{id}", name="question_add_answer")
     */
    public function addAnswerAction(Question $question, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('comment', $question);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $answer->setUser($this->getUser());
            $question->addAnswer($answer);
            $em->persist($answer);
            $em->flush();

            $answerInfo = [
                'id' => $answer->getId(),
                'content' => $answer->getContent(),
                'user' => $answer->getUser()->getUsername(),
                'date' => $answer->getSubmitDate()->format('d-m-Y'),
            ];
            return new JsonResponse(json_encode($answerInfo));
        }
        return new JsonResponse(false);


    }


}