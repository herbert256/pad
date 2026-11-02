<?php

  // capacity - `windows=` records have start,end,resource,capacity. `bookings=` records (or data) have start,end,resource,load. Half-open intervals use calendar normalization: timed boundaries require explicit offsets; allDay uses civil dates in timezone. Supply windows cannot overlap per resource. Used load is summed, not assumed to equal booking count; explicit mode=count opts into count. `unit=` labels all numbers. Remaining/over are nonnegative; zero capacity is valid. A sweep splits each change instant and preserves DST offsets. Maximum 2000 combined intervals.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $capacityWindows=[['resource'=>'Room A','start'=>'2026-11-02T09:00:00+01:00','end'=>'2026-11-02T17:00:00+01:00','capacity'=>3]];$capacityBookings=[['resource'=>'Room A','start'=>'2026-11-02T10:00:00+01:00','end'=>'2026-11-02T12:00:00+01:00','load'=>2],['resource'=>'Room A','start'=>'2026-11-02T11:00:00+01:00','end'=>'2026-11-02T13:00:00+01:00','load'=>2]];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {calendar:capacity windows='capacityWindows', bookings='capacityBookings', unit='staff', timezone='Europe/Amsterdam'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/calendar-capacity.php`.
  //
  // Array-valued options accept a literal array or the quoted name of an application array/store.

include_once PAD.'subs/calendars/_lib/capacity.php';
$padCapacityValue=function($v){if(is_string($v)&&padArrayCheck($v))return padArrayValue($v);return $v;};
try{$rows=isset($padPrm[$pad]['data'])?(array)padChartRows('','capacity'):$padCapacityValue(padTagParm('bookings',[]));$slots=padCalendarCapacity($padCapacityValue(padTagParm('windows',[])),$rows,(string)padTagParm('timezone','UTC'),(string)padTagParm('mode','load'));$padData[$pad]=padDefaultData();$unit=(string)padTagParm('unit','units');$h='<table class="pad-capacity"><caption>'.padCalendarEscape((string)padTagParm('label','Capacity')).' ('.padCalendarEscape($unit).')</caption><thead><tr><th>Resource</th><th>Interval</th><th>Used</th><th>Capacity</th><th>Remaining</th><th>Over capacity</th></tr></thead><tbody>';foreach($slots as $row){$h.='<tr><th scope="row">'.padCalendarEscape($row['resource']).'</th><td>'.padCalendarEscape($row['start'].' – '.$row['end']).'</td>';foreach(['used','capacity','remaining','over'] as $f)$h.='<td>'.padCalendarEscape($row[$f]).'</td>';$h.='</tr>';}return $h.(!$slots?'<tr><td colspan="6">No intervals.</td></tr>':'').'</tbody></table>';}catch(Exception $e){padError($e->getMessage());return '' ;}
