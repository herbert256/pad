<?php

  // page= on a select is the SQL limit whatever its value: an empty page - ?pg= with
  // page=$pg ?? 1 - or a page 0 is the first page, ten rows read and then sorted, the same
  // rows as page 1, a sort= before it or not. Over the 122 customers of the demo database.

  $padSelect ['customerSel'] = [ 'db' => 'customers', 'key' => 'customerNumber' ];

  $none = '';
  $zero = 0;

?>
