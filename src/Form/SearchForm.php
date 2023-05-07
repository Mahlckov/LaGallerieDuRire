<?php

namespace App\Form;

use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('q',TextType::class, [
                    'label'=>'Le nom de la sortie contient : ',
                    'required'=>false,
                    'attr'=>['placeholder'=> 'Rechercher','style' => 'margin-top:1em;','style'=>'margin-bottom:1em;']])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
{
        $resolver->setDefaults([
          'data_class'=> SearchData::class,
                'method'=>'GET',
                'crsf_protection'=>false,

            ]);
}

    public function getBlockPrefix()
    {
        return '';
    }

}