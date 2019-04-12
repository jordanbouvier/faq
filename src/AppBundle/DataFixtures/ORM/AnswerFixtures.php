<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AnswerFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);
        $users = $manager->getRepository(User::class)->findAll();
        $questions = $manager->getRepository(Question::class)->findAll();

        foreach($questions as $question)
        {
            $populator->addEntity(Answer::class, 15, [
                'content' => function() use ($generator) { return $generator->text(500); },
                'submitDate' => function() use ($generator) {return $generator->dateTime(); },
                'isVisible' => 1,
                'user' => function() use ($users) { return $users[array_rand($users)]; },
                'question' => $question,

            ]);
            $populator->execute();

        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            RoleFixtures::class,
            TagFixtures::class,
            QuestionFixtures::class,
        );
    }
}
