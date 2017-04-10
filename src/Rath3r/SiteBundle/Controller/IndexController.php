<?php

namespace Rath3r\SiteBundle\Controller;


class IndexController
{

    public function indexAction()
    {
        return $this->render('lucky/number.html.twig', array('name' => $name));
    }
}
