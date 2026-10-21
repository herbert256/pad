<?php

  // The end of a range a visitor gives - ?n=20000 - chose its length as the whole range
  // would: '1..{$n}' was built whole, 20000 rows, as no request value held all of 1..20000.

  $_GET ['n'] = $n = '20000';

?>
