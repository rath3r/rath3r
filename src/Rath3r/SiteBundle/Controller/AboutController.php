<?php

namespace Rath3r\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{
    public function IndexAction()
    {
        return $this->render('Rath3rSiteBundle:About:index.html.twig');
    }

}
