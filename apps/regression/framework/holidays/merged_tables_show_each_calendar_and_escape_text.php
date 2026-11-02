<?php

include_once PAD . 'subs/calendars/holidays/_lib/holidays.php';
$rows = padHolidaysMerge ( [ 'nl', 'de-by' ], padHolidaysDay ( 2026, 1, 1 ), padHolidaysDay ( 2026, 1, 1 ) );
$rows [0] ['name'] = '<script>name</script>';
$html = padHolidaysMergedTable ( $rows, '<Calendars>' );
$answer = str_contains ( $html, 'Germany, Bavaria' ) && str_contains ( $html, 'Netherlands' )
  && str_contains ( $html, '&lt;script&gt;name&lt;/script&gt;' ) && str_contains ( $html, '&lt;Calendars&gt;' )
  && str_contains ( $html, 'lang="de"' ) && str_contains ( $html, 'lang="nl"' )
  ? 'merged table identifies calendars and escapes names' : 'FAIL';

?>
