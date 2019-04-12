<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Question;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class QuestionFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);
        $users = $manager->getRepository(User::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();
        $populator->addEntity(Question::class, 15, [
            'title' => function() use ($generator) { return $generator->text(255); },
            'content' => function() use ($generator) { return $generator->text(500); },
            'submitDate' => function() use ($generator) {return $generator->dateTime(); },
            'isSolved' => 0,
            'user' => function() use ($users) { return $users[array_rand($users)]; },
            'tags' => function() use ($tags) { return [$tags[array_rand($tags)]]; },


        ]);
        $populator->execute();
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            RoleFixtures::class,
            TagFixtures::class,
        );
    }
}
