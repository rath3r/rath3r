<?php

namespace Rath3r\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        //return $this->render('Resources:Default:index.html.twig');
        return $this->render('default/index.html.twig');
//        $this->render('default/index.html.twig', array(
//            'variable_name' => 'variable_value',
//        ));
    }
}
