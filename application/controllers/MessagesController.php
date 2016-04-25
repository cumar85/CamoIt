<?php
class MessagesController implements IController
{
    private $_fc, $_tpl, $_mm;

    public function __construct()
    {
        $this->_fc = FrontController::getInstance();
        $this->_tpl = new Template();
        $this->_mm = new MessagesModel();
        $this->_tm = new TopicsModel();
        $this->_event = Event::getInstance();
        $this->_pg = new Page();
    }

    public function getmsgsAction()
    {
        $curPage = $this->_fc->getParam('page',1);
        $topicId = $this->_fc->getParam('topicId',1);
        $count = $this->_mm->getCount($topicId);
        $pages = $this->_pg->getPages($curPage, $count, MSGS_ON_PAGE);
        $topicName = $this->_tm->getTopicName($topicId);
        $messages = $this->_mm->getMssages(
                                    $topicId,
                                    $pages['sqlFrom'],
                                    $pages['sqlCount']
                                );
        $errArr = $this->_event->getErr();
        $this->_tm->addVisit($topicId);
        global $startTimePhp;
        $statistic['php']['start'] = $startTimePhp;
        $statistic['sql'] = DB::getInstance()->getSqlStatistic();
        $this->_tpl->assign(array(
                                  'pageName'=>'Список сообщений',
                                  'topicId'=>$topicId,
                                  'statistic'=>$statistic,
                                  'topicName' => $topicName,
                                  'errArr'=>$errArr,
                                  'pages'=>$pages,
                                  'messages' => $messages)
                                );
        $this->_tpl->display(array('header','messages','footer'));
    }

    public function searchAction()
    {
        $query = trim($this->_fc->getParam('query'));
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if($query) {
                header("Location: ".PRJ_URL."/messages/search/query/$query");
            } else {
                header("Location: ".PRJ_URL."/messages/search");
            }
            exit();
        }
        $curPage = $this->_fc->getParam('page',1);
        $count = $this->_mm->getCountSearch($query);
        $pages = $this->_pg->getPages($curPage, $count, MSGS_ON_PAGE);
        $messages = $this->_mm->getSearchMssages(
                                    $query,
                                    $pages['sqlFrom'],
                                    $pages['sqlCount']
                                );
        global $startTimePhp;
        $statistic['php']['start'] = $startTimePhp;
        $statistic['sql'] = DB::getInstance()->getSqlStatistic();

        $errArr = $this->_event->getErr();
        $this->_tpl->assign(array(
                                'pageName'=>'Результаты поиска',
                                'statistic'=>$statistic,
                                'topicName' => $topicName,
                                'errArr'=>$errArr,
                                'pages'=>$pages,
                                'query' => $query,
                                'messages' => $messages)
                                );
        $this->_tpl->display(array('header','search','footer'));
    }

    public function addMsgAction()
    {
        $topicId = $this->_fc->getParam('topicId',false);
        $msgText = trim($this->_fc->getParam('msgText',false));
        $addTime = $this->_mm->addMsg($topicId, $msgText);
        $addTime = Date::getFormatedDateTime($addTime);
        $response = array();
        if ($this->_event->checkErr()) {
            $response['sucsess'] = $this->_event->getSuc();
            $response['addTime'] = $addTime;
        } else {
            $response['error'] = $this->_event->getErr();
        }
        echo json_encode($response);
    }    
}
