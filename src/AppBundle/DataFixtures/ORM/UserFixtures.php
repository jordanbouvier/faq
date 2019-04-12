<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class UserFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);
        $role = $manager->getRepository(Role::class)->findByName('Utilisateur');
        $populator->addEntity(User::class, 20, [
            'username' => function() use ($generator) { return $generator->unique()->userName(); },
            'email' => function() use ($generator) { return $generator->unique()->email(); },
            'password' => '$2y$13$lzaXjrBYWeV.4RA4vrzhb.bzuf6iLCweBxGX01c3r9gcReY/FSVvu',
            'role' => $role[0],
            'isActive' => 1,
            'website' => function() use($generator){ return $generator->unique()->url();},


        ]);
        $populator->execute();
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            RoleFixtures::class,
        );
    }
}
