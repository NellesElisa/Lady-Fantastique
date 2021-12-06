<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Doctrine\DBAL\Types\BooleanType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Choice;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string',TextType::class , [
                'label'=>false,
                'required' => false,
                'attr'=>[
                    'placeholder'=>'agate...',
                    'value'=>'',
                    'class'=>'mb-2 pr-3'
                ]
            ])
            ->add('rechecherN',SubmitType::class, [
                'label'=>'Rechercher',
                'attr'=>[
                    'class'=>'btn btn-info btn-sm'
                ]
            ])
            ->add('categories',EntityType::class , [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('rechercherC',SubmitType::class, [
                'label'=>'trouver par catégories',
                'attr'=>[
                    'class'=>'btn btn-info btn-sm'
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }

}

