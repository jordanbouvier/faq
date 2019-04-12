<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    private $roles = [
      [
          'name' => 'Administrateur',
          'code' => 'ROLE_ADMIN'
      ],
      [
          'name' => 'Utilisateur',
          'code' => 'ROLE_USER',
      ],
      [
          'name' => 'Modérateur',
          'code' => 'ROLE_MODERATOR'
      ]
    ];
    public function load(ObjectManager $manager)
    {
        foreach($this->roles as $role)
        {
            $newRole = new Role();
            $newRole->setName($role['name'])
                ->setCode($role['code']);
            $manager->persist($newRole);
        }
        $manager->flush();
    }
}
