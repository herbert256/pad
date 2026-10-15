<?php

  // Money in the pages: euros with two decimals, grouped by thousands, a real minus sign.

  $shown = implode ( ' | ', array_map ( 'ledgerMoney', [ 0, 12.5, -1234.567, 3200 ] ) );

?>
