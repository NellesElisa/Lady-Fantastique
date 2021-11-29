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

class SearchCatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories',EntityType::class , [
                'label' => 'Les catégories :',
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'attr'=>[
                 'class'=>'row'
                ]
            ])

            ->add('rechercher',SubmitType::class, [
                     'attr'=>[
                            'class'=>'btn btn-info'
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

