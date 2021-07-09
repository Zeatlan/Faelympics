<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('p_pseudo', TextType::class, ['label' => 'Pseudo'])
            ->add('p_faction', ChoiceType::class, [
                'label' => 'Faction',
                'choices' => [
                    'Void Travelers' => 'Void Travelers',
                    'Fukyu Legacy' => 'Fukyu Legacy',
                    'Kazoku' => 'Kazoku'
                ]
                ])
            ->add('p_tag', TextType::class, ['label' => 'Tag Discord'])
            ->add('submit', SubmitType::class, ['label' => 'Créer le membre'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
            "allow_extra_fields" => true
        ]);
    }
}
