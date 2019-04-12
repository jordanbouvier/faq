<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    private $tags = [
        'PHP',
        'JavaScript',
        'HTML',
        'CSS',
        'Python',
        'Ruby',
        'Symfony',
        'React',
        'Bootstrap',
        'jQuery',

    ];
    public function load(ObjectManager $manager)
    {
        foreach($this->tags as $tag)
        {
            $newTag = new Tag();
            $newTag->setName($tag);
            $manager->persist($newTag);
        }
        $manager->flush();
    }
}