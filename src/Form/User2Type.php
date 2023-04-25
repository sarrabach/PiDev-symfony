<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class User2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Name fields
            ->add('userFirstname', TextType::class, [
                'label' => 'First Name','attr'=>['class'=>'form-control']
            ])
            ->add('userLastname', TextType::class, [
                'label' => 'Last Name','attr'=>['class'=>'form-control']
            ])
 
            // Contact fields
            ->add('userMail', EmailType::class, [
                'label' => 'Email','attr'=>['class'=>'form-control']
            ])
            ->add('userPhone', TelType::class, [
                'label' => 'Phone','attr'=>['class'=>'form-control']
            ])
 
            // Login fields
            ->add('username', TextType::class, [
                'label' => 'Username','attr'=>['class'=>'form-control']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password','attr'=>['class'=>'form-control']
            ])
 
            // Role field
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Guide' => 'ROLE_Guide',
                    'Tourist' => 'ROLE_Tourist'
                ]
            ])
            ->add('nationality', CountryType::class, [
                'label' => 'Nationality','attr'=>['class'=>'form-control'],
                'choice_attr' => function($choice, $key, $value) {
                    return ['style' => 'color: black;']; // Add style attribute to change the color of text
                }
            ])
 
            // Additional language field
            ->add('langue', LanguageType::class, [
                'label' => 'Additional Language','attr'=>['class'=>'form-control'],
                'choice_attr' => function($choice, $key, $value) {
                    return ['style' => 'color: black;']; // Add style attribute to change the color of text
                }
            ])
            
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
