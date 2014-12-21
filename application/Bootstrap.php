<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
    	//$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
		//$profiler->setEnabled(true);
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        
        //echo "_initDoctype";
        //$view->headMeta()->appendHttqEquiv('Content-Type', 'text/html');
    }
    
    protected function _initNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        
        $navigation = new Zend_Navigation($config);
        
        $view->navigation($navigation);
    }

    protected function _initTest()
    {
        //echo "_initTest";
    }
    
}

