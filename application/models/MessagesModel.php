<?php
class MessagesModel
{
    private $_db, $_event;

    public function __construct()
    {
        $this->_db = DB::getInstance()->getConnect();
        $this->_event = Event::getInstance();
    }

    public function getCount($topic_id)
    {
        $sql = "SELECT count(*)
                FROM messages
                WHERE topic_id = :topic_id;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getMssages($topic_id, $from, $limit)
    {
        $messages = array();
        if ( !isset($from) or empty($limit) or empty($topic_id) ) {
            return $messages;
        }
        $sql = "SELECT msg, timestamp FROM messages
                WHERE topic_id = :topic_id
                ORDER BY timestamp ASC
                LIMIT  :from, :limit ;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
            $stmt->bindParam(':from', $from, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        $messages = $stmt->fetchall();
        return $messages;
    }

    private function testQueryString($query)
    {
        if ( empty($query) ) {
            $this->_event->add('query','Пустое поле запроса');
        }elseif (strlen($query) < 3) {
            $this->_event->add('query','Запрос слишком короткий');
        }elseif (strlen($query) > 128) {
            $this->_event->add('query','Запрос слишком Длинный');
        }
        if (!$this->_event->checkErr()) {
            return false;
        }
        return true;
    }

    public function getCountSearch($query)
    {
        if(!$this->testQueryString($query)) return false;
        $sql = "SELECT count(*)
                FROM messages
                WHERE msg
                LIKE :search;";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->execute(array(':search' => '%'.$query.'%'));
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getSearchMssages($query, $from, $limit)
    {
        $messages = array();
        if ( !isset($from) or empty($limit) or empty($query) ) {
            return $messages;
        }
        $sql = "SELECT msg, timestamp
                FROM messages
                WHERE  msg LIKE '%$query%'
                ORDER BY timestamp ASC
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
        $messages = $stmt->fetchall();
        return $messages;

    }

    private function testAddMsg($msgText)
    {
        if ( empty($msgText) ) {
            $this->_event->add('msg','Не заполнен текст сообщения');
        }elseif (strlen($msgText) > 255) {
            $this->_event->add('msg','Сообщение слишком длинное');
        }
        if (!$this->_event->checkErr())
        {
            return false;
        }
        return true;
    }

    public function addMsg($topicId, $msgText)
    {
        if(!$this->testAddMsg($msgText) or empty($topicId) )
            return false;
        $sql = "INSERT INTO messages (msg, topic_id, timestamp)
                VALUES (:msgText, :topicId , :timestamp)";
        $addTime = time();
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);
            $stmt->bindParam(':msgText', $msgText);
            $stmt->bindParam(':timestamp', $addTime, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        if ($stmt->rowCount())
        {
            $this->_event->add('msg','Сообщение успешно добавлено', 'SUC');
            return $addTime;
        }
        return false;
    }
}
