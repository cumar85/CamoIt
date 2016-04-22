<?php
class Event {
	private $_errorArr, $_warningArr, $_successArr ;
	static private $_instance = null;
	private function __construct() 
	{
		$this->_errorArr = array();
		$this->_warningArr = array();
		$this->_successArr = array();
		
	}
	public static function getInstance()
	{
		if(self::$_instance == null) {
				self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function add($msgKey, $msg, $type = 'ERR')
	{
		switch($type){
			case 'ERR' : $this->_errorArr["$msgKey"] = $msg; break;
			case 'WRR' : $this->_warningArr["$msgKey"] = $msg; break;
			case 'SUC' : $this->_successArr["$msgKey"] = $msg; break;
			default: $this->_errorArr["$msgKey"] = $msg; break;
		}
	}
	public function checkErr()
	{
		return !(count($this->_errorArr));
	}
	public function getErr($msgKey = null)
	{
		if (empty($msgKey)) {
			return $this->_errorArr;	
		}
		
		if (isset($this->_errorArr["$msgKey"])) {
			return $this->_errorArr["$msgKey"];    
		} else {
			return false;
		}
	}
	public function getSuc($msgKey = null)
	{
		if (empty($msgKey)) {
			return $this->_successArr;	
		}
		
		if (isset($this->_successArr["$msgKey"])) {
			return $this->_successArr["$msgKey"];    
		} else {
			return false;
		}
	}
	public function addLog($class, $method, $e = '', $text = ' ')
	{
		$logString = $class . ' | ' .  $method . ' | '  
		. (empty($e) ? '' :   $e->getMessage())  . 
		' | ' . $text . "\n";
		file_put_contents(ERROR_FILE , $logString , FILE_APPEND);
		
	}
}

	
   


