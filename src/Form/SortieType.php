<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SortieType extends AbstractType
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la sortie : '])
            ->add('dateHeureDebut', DateTimeType::class,[
                'label' => 'Date et heure de la sortie : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, ['label' => 'Nombre de places : '])
            ->add('duree', IntegerType::class, ['label' => 'Durée : '])
            ->add('infosSortie', TextareaType::class, ['label' => 'Description et infos : '])
            ->add('siteOrganisateur', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'data'=> $this->security->getUser()->getCampus()
                ])
            ->add('ville', EntityType::class,[
                'mapped'=> false,
                'label' => 'Ville',
                'class' => Ville::class,
                'choice_label' => 'nom',
            ])
            ->add('lieu',EntityType::class,[
                'label' => 'Lieu',
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('latitude', NumberType::class, ['mapped'=> false])
            ->add('longitude', NumberType::class, ['mapped'=> false])
        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
