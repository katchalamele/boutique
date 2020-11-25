<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['placeholder' => 'Daouda KATCHALA']
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => '10 boulevard Vauban']
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => '59000']
            ])
            ->add('city', TextType::class, [
                'label' => 'Commune',
                'attr' => ['placeholder' => 'Lille']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
