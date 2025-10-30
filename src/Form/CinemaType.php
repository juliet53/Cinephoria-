<?php

namespace App\Form;

use App\Entity\Cinema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CinemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('ville');

            
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data) {
                return;
            }

            //  nettoyage 
            if (isset($data['nom'])) {
                $data['nom'] = strip_tags($data['nom']);
            }

            if (isset($data['ville'])) {
                $data['ville'] = strip_tags($data['ville']);
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cinema::class,
        ]);
    }
}
