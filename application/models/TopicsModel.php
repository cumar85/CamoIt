<?php
class TopicsModel
{
    private $_db, $_event;

    public function __construct()
    {
        $this->_db = DB::getInstance()->getConnect();
        $this->_event = Event::getInstance();
    }

    public function getCount()
    {
        $sql = "SELECT count(*) FROM topics
                WHERE 1";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getTopics($from = 0, $limit = TOPICS_ON_PAGE)
    {
        $topics = $this->getTopicsInDb($from, $limit);
        $counts = $this->getMsgInfoInDb($topics);
        $topics = $this->addMsgInfoToTopics($topics, $counts);
        return $topics;
    }

    public function getTopicsInDb($from, $limit)
    {
        $topics = array();
        if ( !isset($from) or !isset($limit)) {
            return $topics;
        }
        $sql = "SELECT id, title, views FROM topics
                WHERE 1
                ORDER BY id ASC
                LIMIT  :from, :limit ;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':from', $from, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        $topics = $stmt->fetchall();
        return $topics;
    }

    private function getMsgInfoInDb($topics)
    {
        $CountsMessages = array();
        if(!empty($topics)) {
            $idsStr = '';
            foreach($topics as $topic){
                $idsStr .= (int)$topic['id'] . ',';
            }
            $idsStr = '('. substr($idsStr, 0, -1) .')';
        } else {
            return $CountsMessages;
        }

        $sql = "SELECT topic_id, count(*) as cnt, MAX(timestamp) as timestamp
                FROM messages
                WHERE topic_id IN $idsStr
                GROUP BY topic_id ";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        $CountsMessages = $stmt->fetchall();
        return $CountsMessages;
    }

    private function addMsgInfoToTopics($topics , $msgCounts)
    {
        $countsArr = array();
        if(!$topics) {
            return $countsArr;
        }
        foreach ($msgCounts as $count) {
            $countsArr[$count['topic_id']]['cnt'] =  $count['cnt'];
            $countsArr[$count['topic_id']]['timestamp'] = $count['timestamp'];
        }
        foreach ($topics as &$topic) {
            if ( $countsArr[$topic['id']] ) {
                $cnt = $countsArr[$topic['id']]['cnt'];
                $page = new Page();
                $pages = $page->getPages(1, $cnt, MSGS_ON_PAGE);
                $topic['pages'] = $pages;
                $topic['timestamp'] = $countsArr[$topic['id']]['timestamp'];
            } else {
                $topic['pages']= array();
            }
        }
        return $topics;
    }

    public function getTopicName($topicId)
    {
        $sql = "SELECT title
                FROM topics
                WHERE id = :id;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $topicId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB', 'Ошибка базы данных');
        }
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function addVisit($topicId)
    {
        $sql = "UPDATE topics
                SET views = views + 1
                WHERE id = :topicId;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        return $stmt->rowCount();
    }
}
