<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Categorie;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('name', TextType::class)
        ->add('description', TextareaType::class)
        ->add('quantity', IntegerType::class)
        ->add('categorie', EntityType::class, [
            'class' => Categorie::class,

            'choice_label' => 'name',
        ])
        ->add('prix', TextType::class)
        ->add('ttc', CheckboxType::class,[
            'mapped' => false,
            'label' => 'Ajouter TVA',
            'required' => $options['required_ttc']
        ])
        
        ->add('image',FileType::class,[
            'mapped' => false,
            'required' => false,

            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'le fomrat invalid',
                ])
            ],
        ])

        ->add('save', SubmitType::class, ['label' => 'valider'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'required_ttc' =>false
        ]);
    }
}


?>