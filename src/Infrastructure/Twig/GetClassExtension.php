<?php

namespace App\Twig;

use ReflectionClass;
use Twig_Extension;
use Twig_SimpleFunction;

class GetClassExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            'class' => new Twig_SimpleFunction('class', [$this, 'getClass']),
        ];
    }

    public function getName()
    {
        return 'class_twig_extension';
    }

    public function getClass($object): string
    {
        return (new ReflectionClass($object))->getShortName();
    }
}
