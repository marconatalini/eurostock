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
use Vich\UploaderBundle\Form\Type\VichImageType;

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
                    'data-help-message' => $this->helpMessage(),
                ]

            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Immagine',
                'allow_delete' => false,
                'download_uri' => true,
                'required' => $options['image_required'],
                'attr' => [
                    'accept' => 'image/jpeg'
                ]
            ])
            ->add('tags', TagsInputType::class, [
                'required' => false,
                'help' => 'Etichette per la ricerca, divisi da una virgola.'
            ])
            ->add('description', TextareaType::class, [
                'label' => ' Descrizione',
                'required' => false,
                'help' => 'Descrizione completa della foto, utile anche per una futura ricerca.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'image_required' => true,
        ]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('image_required', 'bool');
    }

    private function helpMessage()
    {
        return json_encode($this->categoryRepository->findDaysBeforeDelete());
    }

}
