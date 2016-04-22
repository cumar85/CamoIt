<?
class Calendar 
{
	private $_month;
	private $_year;
	private $_day;
    
  public function __construct($month = null,$year = null, $day = null) 
  {
		$month = (int)$month;
		$year = (int)$year;
		$day = (int)$day;
		
		$this->_day = ($day and  $day < 31 ) ? $day : date('j');
		$this->_month = ($month and  $month < 14) ? $month : date('n');
		$this->_year = ($year and  $year < 2038) ? $year : date('Y');
  }
	public function getDay()
	{
		return $this->_day;
	}
	public function getCurDayTimeStamp($h = 0,$m = 0,$s = 0 )
	{
		return mktime($h, $m, $s, $this->_month, $this->_day, $this->_year);
	}
	
	public function getDays() 
	{
			
			$firstday_num = date('N' ,mktime(0, 0, 0, $this->_month, 1, $this->_year))-1;    
			$lastday = date('d' ,mktime(0, 0, 0, $this->_month+1, 0, $this->_year));
			
			$month_days = array();
			
			for($day = 1,$week = 1,$day_num = 1; ; $day_num++) {
					if(($day_num > $firstday_num or $week > 1) && $day <= $lastday) {
							$month_days[$week][$day_num] = $day; $day++;   
					} else {
							$month_days[$week][$day_num] = '';
					}
					if($day > $lastday AND $day_num == 7) {
							break;
					}
					if($day_num == 7) {
							$week++; $day_num=0;   
					}
			}
			return $month_days;
	}
	public function	isCurDay($day)
	{
		if ($this->_month != date('n'))
		return false;
		
		if ($this->_year !=  date('Y'))
		return false;
		
		if ($day != date('j'))
		return false;
		
		return true;
	}
	public function getMonthNum() 
	{
		return date('n',mktime(0, 0, 0, $this->_month, 1, $this->_year));
	}	
		
	public function getMonthName() 
	{
		return $this->getMonthNames($this->getMonthNum());
	}
		
	public function getNextMonth() 
	{
		return date('n',mktime(0, 0, 0, $this->_month+1, 1, $this->_year));
	}	
	
	public function getPrevMonth() 
	{
		return date('n',mktime(0, 0, 0, $this->_month-1, 1, $this->_year));
	}
	

	public function getYear()
	{
		return $this->_year;
	}
	
	public static function getMonthNames($month=null) 
	{
			if(!$month) {
				return array(
					1 => "Январь",
					2 => "Февраль",
					3 => "Март",
					4 => "Апрель",
					5 => "Май",
					6 => "Июнь",
					7 => "Июль",
					8 => "Август",
					9 => "Сентябрь",
					10 => "Октябрь",
					11 => "Ноябрь",
					12 => "Декабрь", 
				);
			} else {
				switch($month) {
					case 1: return 'Январь'; break;
					case 2: return 'Февраль'; break;
					case 3: return 'Март'; break;
					case 4: return 'Апрель'; break;
					case 5: return 'Май'; break;
					case 6: return 'Июнь'; break;
					case 7: return 'Июль'; break;
					case 8: return 'Август'; break;
					case 9: return 'Сентябрь'; break;
					case 10: return 'Октябрь'; break;
					case 11: return 'Ноябрь'; break;
					case 12: return 'Декабрь'; break;
					default : return 'Ошибка'; break;
				}			
			}    
			
	}
	public static function getDaysNames() 
	{
			return array(
						'Понедельник',
						'Вторник',
						'Среда',
						'Четверг',
						'Пятница',
						'Суббота',
						'Воскресенье'
			);
	}
	
}