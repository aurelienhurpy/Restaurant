<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',DateType::class,['widget'=>'single_text','label'=>'Date de réservation'])
            ->add('time',TimeType::class,['widget'=>'single_text','label'=>'Horaire'])
            ->add('booker',EntityType::class,[
                    'class'=> User::class,
                    'choice_label'=> function($user){return $user->getFirstname(). " ".strtoupper($user->getLastname());},
                    'label'=>'Client'
                    ])
            ->add('guest',IntegerType::class,['label'=>'Nombre de couverts','required'=>true])
                     ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'validation_groups'=>['Default']
        ]);
    }
}
