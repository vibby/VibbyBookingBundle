<?php

namespace Vibby\Bundle\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('attr' => array(
                'label' => 'Nom',
                'class' => 'pregValidate',
                'title' => 'Votre prénom',
                'data'  => '/^.[^\x00-\x1F\x7F]{2,}$/i',
                )))

            ->add('email', 'email', array('attr' => array(
                'label' => 'Email',
                'class' => 'pregValidate',
                'title' => 'Votre email',
                'data'  => '/^[a-z0-9_\+-]+(\.[a-z0-9_\+.-]+)*@[a-z0-9-]+(\.[a-z0-9.-]+)*\.([a-z]{2,6})$/i',
                )))
            ->add('message', 'textarea', array('attr' => array(
                'label' => 'Message',
                'class' => 'pregValidate',
                'title' => 'Votre message',
                'data'  => '/^.[^\x00-\x1F\x7F]{2,}$/i',
                )))
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'Name should not be blank.')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'Email should not be blank.')),
                new Email(array('message' => 'Invalid email address.'))
            ),
            'message' => array(
                new NotBlank(array('message' => 'Message should not be blank.')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}
