<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SearchSortiesType extends AbstractType
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'data'=> $this->security->getUser()->getCampus()
                ])
            ->add('recherche', TextType::class,[
                'label'=>'Le nom de la sortie contient',
                'required' => false,
                'attr'=> [
                    'placeholder' => 'Rechercher'
                    ]
                ])
            ->add('debutPeriode', DateType::class, [
                'label'=>'Entre',
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
                ])
            ->add('finPeriode', DateType::class, [
                'label'=>'Et',
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
                ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur·rice',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e',
                'required' => false,
            ])
            ->add('pasInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e',
                'required' => false,
            ])
            ->add('past', CheckboxType::class, [
                'label' => 'Sortie passées',
                'required' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
