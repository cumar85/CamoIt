<?
class Date
{
    public static function getFormatedDateTime($timestamp)
    {
        if (!isset($timestamp) or $timestamp < 1) {
            return '';
        }
        $time = date("H:i" ,$timestamp);
        $dayName = self::getDaysName(date("N" ,$timestamp));
        $dayNum  = date("d" ,$timestamp);
        $monthName =  self::getMonthName(date("n" ,$timestamp));
        $year = date("Y" ,$timestamp);
        $time = date("H:i" ,$timestamp);
        $todayTimestamp = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
        $thisdayTimestamp = mktime(0, 0, 0, date("n",$timestamp), $dayNum, $year);
        if ( $todayTimestamp == $thisdayTimestamp ) {
            return "Сегодня, $time";
        }
        return "$dayName $dayNum  $monthName,  $year  $time ";
    }

    public static function getMonthName($monthNum)
    {
        $nameMonth = array(
            '1' => 'Янв',
            '2' => 'Фев',
            '3' => 'Мрт',
            '4' => 'Апр',
            '5' => 'Май',
            '6' => 'Июн',
            '7' => 'Июл',
            '8' => 'Авг',
            '9' => 'Сен',
            '10' => 'Окт',
            '11' => 'Ноя',
            '12' => 'Дек'
        );
        return $nameMonth[$monthNum];
    }

    public static function getDaysName($dayNum)
    {
        $nameWeek = array(
            '1' => 'Пн',
            '2' => 'Вт',
            '3' => 'Ср',
            '4' => 'Чт',
            '5' => 'Пт',
            '6' => 'Сб',
            '7' => 'Вс'
        );
        return $nameWeek[$dayNum];
    }
}
