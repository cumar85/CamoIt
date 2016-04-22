<?php
class IndexController implements IController 
{
	private $_fc, $_tpl, $_tm, $_mm, $_event, $_pg;
	public function __construct() 
	{
		$this->_fc = FrontController::getInstance();
		$this->_tpl = new Template();
		$this->_tm = new TopicsModel();
		$this->_mm = new MessagesModel();
		$this->_event = Event::getInstance();
		$this->_pg = new Page();
	}
	public function indexAction() 
	{
		$count = $this->_tm->getCount();
		$curPage = $this->_fc->getParam('page',1);
		$topicPages = $this->_pg->getPages($curPage, $count, TOPICS_ON_PAGE);
		$topics = $this->_tm->getTopics($topicPages['sqlFrom'],$topicPages['sqlCount']);
		$errArr = $this->_event->getErr();
		$statistic = array();
		global $startTimePhp;
		$statistic['php']['start'] = $startTimePhp;
		$statistic['sql'] = DB::getInstance()->getSqlStatistic();
		$this->_tpl->assign(array('pageName'=>'Список тем',
															'errArr'=>$errArr,
															'topics'=>$topics,
															'statistic'=>$statistic,
															'topicPages' => $topicPages)
														);   
		$this->_tpl->display(array('header','index','footer'));
	}
}