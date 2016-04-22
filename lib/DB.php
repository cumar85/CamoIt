<?
class DB 
{
    private $_db, $_event;
    static private $_instance = null;
    private function __construct() 
    {
        $this->_event = Event::getInstance();
        try {   
            $this->_db = new PDO('mysql:host='.DB_HOST, DB_USER, DB_PASSWORD);
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
            $this->_event->add('DB','Ошибка базы данных');
        }
        try {
            $this->_db->exec("SET NAMES " . DB_CHARSET);
            $this->_db->exec("USE ". DB_NAME);
            $this->_db->exec("set profiling=1;");
        } catch (PDOException $e) {
            $error = $this->_db->errorInfo();
            if($error[1] == 1049) {
                $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
                $this->_event->add('NO_DB',"Базы данны '".DB_NAME. "' не существует");
              //die("Базы данны".DB_NAME. "не существует");
            } else {
				$this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
                $this->_event->add('DB','Ошибка базы данных');
            }
        }
    }
	private function __clone() { }
    public function __destruct() 
    {
        $this->_db = null;
    }
	public static function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function getConnect()
    {
        return $this->_db; 
    }
	public function getSqlStatistic()
	{
		$statisticSQL = array();
		$profiling = $this->getProfiling();
		$statisticSQL['count'] = count($profiling);
		$statisticSQL['duration'] = 0;
		foreach ($profiling as $row) {
			$statisticSQL['duration'] += $row['Duration'];
		}
		return $statisticSQL;
	}
    private function getProfiling()
	{
		$sql = "show profiles;";
		try {
				$stmt = $this->_db->prepare($sql);
				$stmt->execute();
			} catch (PDOException $e) {
			$this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
			$this->_event->add('DB','Ошибка базы данных');
		}
		return $stmt->fetchall(PDO::FETCH_ASSOC);
	}
}
