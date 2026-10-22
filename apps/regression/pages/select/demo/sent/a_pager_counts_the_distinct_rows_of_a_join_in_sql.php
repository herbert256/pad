<?php

  // The {pager} of a joined select whose select list decides what a row is counts its rows
  // in SQL, over the select as a derived table, when no column name in that list repeats:
  // every joined one was read row by row into PHP - 170 MB for 350,000 distinct rows. The
  // statements db() sent are kept by _events/sql.php. Over the Classic Models tables of the
  // demo database.

  $padSelect ['orderSel'] = [ 'db' => 'orders', 'key' => 'orderNumber' ];

?>
