<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr'  => ['placeholder' => 'Tapez le nom du produit']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'attr' => ['placeholder' => 'Tapez le prix du produit en €'],
                'divisor' => 100

            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'URL de l\'image',
                'attr'  => ['placeholder' => 'Tapez une URL d\'image']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Courte description',
                'attr'  => ['placeholder' => 'Tapez une courte description']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '--Choisir une catégorie--',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);

        //$builder->get('price')->addModelTransformer(new CentimesTransformer);

        // $builder->get('price')->addModelTransformer(new CallbackTransformer(
        //     function ($value) {
        //         if ($value === null) return;
        //         return $value / 100;
        //     },
        //     function ($value) {
        //         if ($value === null) return;
        //         return $value * 100;
        //     }
        // ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
