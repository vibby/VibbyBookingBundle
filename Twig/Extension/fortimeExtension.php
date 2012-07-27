<?php

namespace Vibby\Bundle\BookingBundle\Twig\Extension;

use Vibby\Bundle\BookingBundle\Twig;

class fortimeExtension extends Twig_Extension
{
    public function getTokenParsers()
    {
        return array(new Vibby\Bundle\BookingBundle\Twig\Twig_TokenParser_Fortime());
    }

}