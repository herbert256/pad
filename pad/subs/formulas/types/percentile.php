<?php

  // percentile - PERCENTILE(array, k) is PERCENTILE.INC under the name it had before Excel 2010,
  // which Excel, LibreOffice and Google Sheets still read: the k-th percentile, k from 0 to 1,
  // interpolated between the numbers at the place k*(n-1)+1.
  //
  //   =PERCENTILE(B2:B7, 0.3)         6.5
  //   =PERCENTILE(C2:C7, 0.5)         1.725

  return include __DIR__ . '/percentile.inc.php';

?>
