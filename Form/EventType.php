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
                'format' => 'yyyy-MM-dd',
                'attr' => array('class' => 'date', 'style' => 'display:none;'),
		))
            ->add('date_to','date',array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd',
                'attr' => array('class' => 'date', 'style' => 'display:none;'),
                ))
            ->add('firstname', 'text', array('attr' => array(
                'Placeholder' => 'Votre Prénom',
                'class' => 'pregValidate',
                'title' => 'Votre prénom',
                'data'  => '/^.[^\x00-\x1F\x7F]{2,}$/i',
                )))
            ->add('lastname', 'text', array('attr' => array(
                'Placeholder' => 'Votre nom',
                'class' => 'pregValidate',
                'title' => 'Votre nom',
                'data'  => '/^.[^\x00-\x1F\x7F]{2,}$/i',
                )))
            ->add('email', 'email', array('attr' => array(
                'Placeholder' => 'Votre email',
                'class' => 'pregValidate',
                'title' => 'Votre email',
                'data'  => '/^[a-z0-9_\+-]+(\.[a-z0-9_\+.-]+)*@[a-z0-9-]+(\.[a-z0-9.-]+)*\.([a-z]{2,6})$/i',
                )))
            ->add('phone', 'text', array('attr' => array(
                'Placeholder' => 'Votre téléphone',
                'class' => 'pregValidate',
                'title' => 'Votre téléphone',
                'data'  => '/^0[0-69]([_ -./\\ ]?[0-9]{2}){4}$/i',
                )))
//            ->add('is_validated')
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
        return 'booking';
    }
}
