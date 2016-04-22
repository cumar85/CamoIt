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
            //$this->_db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
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
        //$this->setProfiling();
    }
    
    public function __destruct() 
    {
        $this->_db = null;
    }
    public function getProfiling()
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
		public  function getSqlStatistic()
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
		
    public static function delete($table, $id)
		{
			 $event = Event::getInstance();
			 if (!empty($id)) {
				$db = self::getInstance()->getConnect();
				$sql = "DELETE FROM $table 
							WHERE id = :id; ";
				try {
							$stmt = $db->prepare($sql);
							$stmt->bindValue(':id', $id);
							$stmt->execute();
						} catch (PDOException $e) {
							file_put_contents(ERROR_FILE, $e->getMessage()."\n", FILE_APPEND);
							$this->_event->add('DB','Ошибка базы данных');
							return false;
						}
				} else {
					$this->_event->addLog(__CLASS__ ,  __METHOD__, $e, 'id is empty');
					$this->_event->add('DB','Ошибка базы данных');
					return false;
			 }
			 return $db->lastInsertId();	
		}
		
    public static function insert($table, $dataArr)
    {
				$event = Event::getInstance();
        $db = self::getInstance()->getConnect();
        if (!empty($dataArr)) {
					$fields ='';
					$values ='';
					$isfirst = true;
					foreach($dataArr as $k=>$v) {
							if($isfirst) {
								 $isfirst = false; 
							} else {
									$fields .= ', '; $values .= ', ';   
							}
							$fields .= $k; $values .= ':'.$k;
					}
					$sql = "INSERT INTO $table ( $fields )
							VALUES ( $values );";
				 
					
							try {
									$stmt = $db->prepare($sql);
									foreach ($dataArr as $k=>$v) {
											$stmt->bindValue(':'.$k, $v);
									}
									$stmt->execute();
							} catch (PDOException $e) {
								$this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
								$this->_event->add('DB','Ошибка базы данных');
								return false;
							}
        } else {
			$this->_event->addLog(__CLASS__ ,  __METHOD__, $e,'dataArr is empty');
			$this->_event->add('DB','Ошибка базы данных');
			return false;
        }
        return $db->lastInsertId(); 
    }
  
    public static function update($table, $id, $dataArr)
    {
				$db = self::getInstance()->getConnect();
        if (!empty($dataArr)) {
        $set ='';
        $isfirst = true;
        foreach($dataArr as $k=>$v) {
            if($isfirst) {
               $isfirst = false; 
            } else {
                $set .= ', ';   
            }
            $set .= $k.' = :'.$k;
        }
        $sql = "UPDATE  $table SET $set
           WHERE id = :id;";
            try {
                $stmt = $db->prepare($sql);
                    $stmt->bindValue(':id', $id);
                foreach ($dataArr as $k=>$v) {
                    $stmt->bindValue(':'.$k, $v);
                }
                $stmt->execute();
            } catch (PDOException $e) {
                $this->_event->addLog(__CLASS__ ,  __METHOD__, $e);
				$this->_event->add('DB','Ошибка базы данных');
				return false;
            }
        } else {
			$this->_event->addLog(__CLASS__ ,  __METHOD__, $e, 'dataArr is empty');
			$this->_event->add('DB','Ошибка базы данных');
			return false;
        }
        return true;
    }
}
