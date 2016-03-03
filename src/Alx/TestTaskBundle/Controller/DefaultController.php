<?php

namespace Alx\TestTaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AlxTestTaskBundle:Default:index.html.twig');
    }
}
