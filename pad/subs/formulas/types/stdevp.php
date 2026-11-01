<?php

  // stdevp - STDEVP(number, ...) is STDEV.P under the name it had before Excel 2010, which Excel,
  // LibreOffice and Google Sheets still read: the standard deviation of a whole population.
  //
  //   =STDEVP(2, 4, 4, 4, 5, 5, 7, 9)    2

  return include __DIR__ . '/stdev.p.php';

?>
