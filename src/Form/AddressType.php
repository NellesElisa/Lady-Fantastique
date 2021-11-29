<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Quel nom souhaitez-vous donner a votre adresse ?',
                'attr'=>[
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Votre prénom',
                'attr'=>[
                    'placeholder' => 'Entrer votre prénom'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Votre nom',
                'attr'=>[
                    'placeholder' => 'Entrer votre nom'
                ]
            ])
            ->add('company', TextType::class,[
                'label' => 'Votre société',
                'required' => false,
                'attr'=>[
                    'placeholder' => '(facultatif) Entrer votre société'
                ]
            ])
            ->add('address', TextType::class,[
                'label' => 'Votre adresse',
                'attr'=>[
                    'placeholder' => '5 rue de la république'
                ]
            ])
            ->add('postal', TextType::class,[
                'label' => 'Votre code postal',
                'attr'=>[
                    'placeholder' => '13013'
                ]
            ])
            ->add('city', TextType::class,[
                'label' => 'Votre ville',
                'attr'=>[
                    'placeholder' => 'Marseille'
                ]
            ])
            ->add('country', CountryType::class,[
                'label' => 'Votre pays',
                'attr'=>[
                    'placeholder' => 'France'
                ]
            ])
            ->add('phone', TelType::class,[
                'label' => 'votre téléphone',
                'attr'=>[
                    'placeholder' => '06 ********'
                ]
            ])
            ->add('submit' ,SubmitType::class,[
                'label' =>'Valider',
                'attr'=>[
                    'class' => 'btn-block btn-dark',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
