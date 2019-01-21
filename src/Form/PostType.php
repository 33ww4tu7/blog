<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header',  null, ['attr' => [
                'class' => 'form-control', 'placeholder' => 'Title'
            ]])
            ->add('body',  TextareaType ::class, ['attr' => [
                'class' => 'form-control', 'placeholder' => 'Body', "rows" => "15"
            ]])
            ->add('image', FileType::class, ['data_class' => null,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
