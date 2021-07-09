<?php

namespace App\Form;

use App\Entity\Discipline;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DisciplineType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('game', TextType::class, ['attr' => ['placeholder' => 'Habbo Hôtel'], 'label' => 'Jeu'])
            ->add('format', ChoiceType::class, [
                'choices' => [
                    '1v1' => '1v1',
                    'FFA' => 'FFA',
                    'TeamVSTeam' => 'TeamVSTeam'
                ],
            ])
            ->add('banner', TextType::class, ['label' => 'Image de fond (URL)', 'attr' => ['placeholder' => 'https://thumbs.gfycat.com/AdmirableUntimelyGharial-size_restricted.gif']])
            ->add('streamed', ChoiceType::class, [
                'label' => 'La discipline sera-t\'elle streamée ?',
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0
                ],
                'empty_data' => false
            ])
            ->add('description', TextareaType::class, ['label' => 'Description de votre discipline', 'required' => false]);

        // Edition
        if(isset($options['empty_data']) && ($options['empty_data'] == "1v1" || $options['empty_data'] == 'TeamVSTeam' || $options['empty_data'] == 'FFA')){
                $builder->add('default', IntegerType::class, ['label' => 'Points par défaut (Position x coefficient)+Points', 'data' => (int)$builder->getData()->getDefault()])
                    ->add('coeff', NumberType::class, ['label' => 'Coefficient', 'data' => (float)$builder->getData()->getCoeff()])
                    ->add('start_date', DateTimeType::class, [
                        'label' => 'Date du début',
                        'widget' => 'single_text',
                        'input' => 'string',
                        'format' => 'dd/MM/yyyy',
                        'attr' => [
                            'placeholder' => 'dd/MM/yyyy'
                        ],
                        'data' => date('Y-m-d H:i:s', $builder->getData()->getStartDate())
                    ]);
        }else{
            $builder->add('default', IntegerType::class, ['label' => 'Points par défaut (Position x coefficient)+Points'])
                ->add('coeff', NumberType::class, ['label' => 'Coefficient', 'data' => 1])
                ->add('start_date', DateTimeType::class, [
                    'label' => 'Date du début',
                    'widget' => 'single_text',
                    'input' => 'string',
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'placeholder' => 'dd/MM/yyyy'
                    ],
                    'data' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
        }
            $builder->add('save', SubmitType::class, ['label' => 'Créer']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Discipline::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'new_discipline',
            "allow_extra_fields" => true
        ]);
    }
}
