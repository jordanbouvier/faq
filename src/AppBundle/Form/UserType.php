<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Url;


class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['register'] || $options['admin'])
        {
            $builder->add('username', null, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide',

                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 12,
                        'minMessage' => "Le pseudo doit faire 4 caractères au minimum",
                        'maxMessage' => "Le pseudo doit faire 12 caractères au maximum"
                    ]),


                ]
            ]);
        }
            $builder->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide',
                    ]),
                    new Email([
                        'message' => 'L\'email n\'est pas valide'
                    ]),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Répetez le mot de passe'),
                'constraints' => [
                    ($options['admin'] || !$options['register'] ? new Optional() : new NotBlank([
                        'message' => 'Ce champ ne doit pas être vide',
                    ])),
                    new Length([
                        'min' => '5',
                        'max' => '15',
                        'minMessage' => 'Le mot de passe doit faire 5 caractères minimum',
                        'maxMessage' => 'Le mot de passe doit faire 15 caractères maximum',

                    ])
                ]
            ])
            ->add('website', null, [
                'label' =>  'Site internet',
                'constraints' => [
                    new Url(),
                    new Optional(),
                ]
            ]);
            if($options['admin'])
            {
                $builder->add('isActive')
                ->add('role');
            }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'admin' => false,
            'register' => false,
            'attr' => ['novalidate' => 'novalidate']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
