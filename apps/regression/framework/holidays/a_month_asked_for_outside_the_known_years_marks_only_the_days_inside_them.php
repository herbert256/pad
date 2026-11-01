<?php

  // The month a visitor asks for - the request value the calendar's own links set - is no
  // fault of the template: outside the years the country's file knows the calendar shows it
  // with only the days of its grid inside those years marked. It answered an error page,
  // and the link to the month before South Africa's first year, on January 2014, led there.
  // No grid marks a day outside those years: December 2052's last week leaves New Zealand's
  // 1 and 2 January 2053 unmarked, as January 2053 shows them, where the rules marked them.

  $_GET ['month'] = '2013-12';
  $_GET ['later'] = '2053-06';

  $month = padRequest ( 'month', padNow ( 'Y-m' ) );

?>
