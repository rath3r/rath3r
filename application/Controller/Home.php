<?php

class Controller_Home extends Controller_Default
{
	public $name = "rath3r";
	
    public function indexAction()
    {
        $this->smarty->assign("bodyClass", "home");
        $this->smarty->assign("title", $this->name . " > Home");
        parent::indexAction();
    }
}
