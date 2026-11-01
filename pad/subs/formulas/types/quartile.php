<?php

  // quartile - QUARTILE(array, quart) is QUARTILE.INC under the name it had before Excel 2010,
  // which Excel, LibreOffice and Google Sheets still read: 0 the smallest number, 1 the first
  // quartile, 2 the median, 3 the third quartile, 4 the largest.
  //
  //   =QUARTILE(B2:B7, 1)             5.75
  //   =QUARTILE(B2:B7, 4)             20

  return include __DIR__ . '/quartile.inc.php';

?>
