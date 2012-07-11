<?php

namespace Vibby\Bundle\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_from','date',array(
		'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class' => 'date', 'style' => 'display:none;'),
		))
            ->add('date_to','date',array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class' => 'date', 'style' => 'display:none;'),
                ))
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('phone')
            ->add('is_validated')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vibby\Bundle\BookingBundle\Entity\Event'
        ));
    }

    public function getName()
    {
        return 'vibby_bundle_bookingbundle_eventtype';
    }
}
