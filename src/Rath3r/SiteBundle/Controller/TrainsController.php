<?php

namespace Rath3r\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrainsController extends Controller
{
    public function IndexAction()
    {
        return $this->render('trains/index.html.twig');
    }

}
