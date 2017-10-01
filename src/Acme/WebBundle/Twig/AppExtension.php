<?php

// src/AppBundle/Twig/AppExtension.php

namespace Acme\WebBundle\Twig;

class AppExtension extends \Twig_Extension {

    public function getGlobals() {
        
        return array(
            'holamundo' => "Super Pendejo",
        );
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        );
    }

    public function priceFilter() {
        return "ajajajajajajjaa";
    }

    public function getName() {
        return 'app_extension';
    }
}
