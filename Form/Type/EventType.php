<?php

namespace Vibby\Bundle\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormValidatorInterface;


class EventType extends AbstractType 
{
    public function getDefaultOptions(array $options)
    {
        return array('placeholder' => null);
    }

    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'Placeholder';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('placeholder', $options['placeholder']);
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('placeholder', $form->getAttribute('placeholder'));
    }
}
