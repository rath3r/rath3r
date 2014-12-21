<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
       //echo "init";
       //$uri = $this->_request->getPathInfo();
       //$activeNav = $this->view->navigation()->findByUri($uri);
       //$activeNav->active = true;
       //var_dump($activNav);
    }

    public function indexAction()
    {
        // action body
        // action body
        try {
            //$maps = new Application_Model_BlogMapper();
            //$this->view->list = $maps->fetchAll();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function aboutAction()
    {
        // action body
    }
    
    public function contactAction()
    {
        // action body
    }

}
