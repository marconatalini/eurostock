<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Image;
use App\Form\Type\TagsInputType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'scegli una categoria',
                'label' => 'Categoria',
                'help' => 'La foto rimarrà disponibile per un certo numero di giorni',
                'attr' => [
                    'data-help-message' => $this->helpMessage()
                ]

            ])
            ->add('imageFile', FileType::class, [
                'attr' => [
                    'accept' => 'image/jpeg'
                ]
            ])
            ->add('tags', TagsInputType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }

    private function helpMessage()
    {
        return json_encode($this->categoryRepository->findDaysBeforeDelete());
    }

}
