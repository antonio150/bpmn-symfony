<?php

namespace App\Form;

use App\Entity\GroupeOrganisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeOrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameGroupeOrg', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('displaynameGroupeOrg', TextType::class, [
                'label' => 'Surnom',
                'required' => true,
            ])
            ->add('descriptionNameGroupeOrg', TextareaType::class, [
                'label' => 'Déscription',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupeOrganisation::class,
        ]);
    }
}
