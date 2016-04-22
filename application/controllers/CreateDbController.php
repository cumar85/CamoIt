<?php
class CreateDbController implements IController 
{
	private $_fc, $_tpl, $_um;
	public function __construct() 
	{
		$this->_fc = FrontController::getInstance();
		$this->_tpl = new Template();
	}
	public function indexAction() 
	{
		
		
		$this->_tpl->assign(array('prjName'=>PRJ_NAME,'pageName'=>'Создание БД'));   
		$this->_tpl->display(array('header','createDb','footer'));
		
	}
}