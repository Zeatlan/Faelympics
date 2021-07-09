<?php

namespace App\Form;

use App\Entity\Discipline;
use App\Entity\Player;
use App\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('p_pseudo', EntityType::class, [
            'label' => 'Pseudo',
            'class' => Player::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->orderBy('p.p_pseudo', 'ASC');
            },
        ])
            ->add('save', SubmitType::class, ['label' => 'Inscrire'])
            ->add('desinscrire', SubmitType::class, ['label' => 'Désinscrire'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
        ]);
    }
}
