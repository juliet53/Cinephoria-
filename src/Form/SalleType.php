<?php

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('capacite')
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,        
                'choice_label' => 'nom',         
                'placeholder' => 'Sélectionner un cinéma',  
                'required' => true,              
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data) {
                return;
            }

            //  nettoyage 
            if (isset($data['numero'])) {
                $data['numero'] = strip_tags($data['numero']);
            }

            $event->setData($data);
        });
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
