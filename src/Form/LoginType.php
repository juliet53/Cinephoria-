<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                    'autofocus' => true,
                ],
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                ],
                'required' => true,
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'Se souvenir de moi',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('loginType', HiddenType::class, [
                'mapped' => false,
                'data' => $options['loginType'],
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'authenticate',  
            
        ]);
            $resolver->setDefined('loginType'); // autoriser l'option loginType
            $resolver->setDefault('loginType', 'ROLE_USER'); // valeur par défaut    
    }
    public function getBlockPrefix()
    {
        return ''; // Pour ne PAS avoir de préfixe sur les noms de champs
    }
    
}