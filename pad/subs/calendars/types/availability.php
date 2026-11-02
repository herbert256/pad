<?php
// {calendar:availability windows=$working, data='bookings', timezone='Europe/Amsterdam', minimum=30/}
include_once PAD.'subs/calendars/_lib/availability.php';
try {
 $windows=padTagParm('windows',[]);$minimum=padTagParm('minimum',30);$timezone=(string)padTagParm('timezone','UTC');
 if(!is_array($windows)||filter_var($minimum,FILTER_VALIDATE_INT)===FALSE)throw new InvalidArgumentException('Availability needs windows and a whole minimum');
 $rows=isset($padPrm[$pad]['data'])?(array)padChartRows('','availability'):[];$slots=padCalendarAvailability($windows,$rows,$timezone,(int)$minimum);$padData[$pad]=padDefaultData();
 $html='<section class="pad-availability"><h2>'.padCalendarEscape((string)padTagParm('label','Available times')).'</h2><p>Timezone: '.padCalendarEscape($timezone).'</p>';
 if(!$slots)return $html.'<p>No available intervals.</p></section>';
 $html.='<ul>';foreach($slots as $slot)$html.='<li><strong>'.padCalendarEscape($slot['resource']).'</strong> <time datetime="'.padCalendarEscape($slot['start']).'">'.padCalendarEscape($slot['start']).'</time> – <time datetime="'.padCalendarEscape($slot['end']).'">'.padCalendarEscape($slot['end']).'</time> ('.$slot['minutes'].' minutes)</li>';
 return $html.'</ul></section>';
}catch(Exception $e){padError($e->getMessage());return '';}
?>
