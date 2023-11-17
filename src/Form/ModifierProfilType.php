<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class ModifierProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('pseudo', TextType::class, ['constraints'=>[
                new NotBlank(['message'=>'Veuillez rensigner un Pseudo']),
                new Length(['min'=>4, 'minMessage'=>'Votre pseudo doit faire au moins {{ limit }} caractères.']),
                new Regex([
                    'pattern' => '~^[a-zA-Z0-9_.-]+$~',
                    'message' => 'Le pseudo ne doit contenir que des caractères alphanumériques non accentués et ".", "-" et "_".'])]])

            ->add('prenom')

            ->add('nom')

            ->add('telephone')

            ->add('mail', EmailType::class, ['constraints' => [
                new NotBlank(['message'=> 'Veuillez renseigner une adresse E-mail.']),
                new Email(['message'=> 'Adresse E-mail invalide.'])]])

            ->add('motPasse', RepeatedType::class, [
                'type'=>PasswordType::class,
                'invalid_message'=> 'Les mots de passe ne correspondent pas.',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new Length([
                        'min'=>6,
                        'minMessage'=>'Le mot de passe doit faire au moins {{ limit }} caractères'
                    ])]])

            ->add('campus', EntityType::class, ['class'=>Campus::class, 'choice_label'=>'nom'])

            ->add('photoProfil', FileType::class, ['label'=>'Photo de profil', 'mapped'=>false, 'required'=>false]);





    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
