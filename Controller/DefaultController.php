<?php

namespace Ekyna\Bundle\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EkynaTableBundle:Default:index.html.twig', array('name' => $name));
    }
}
