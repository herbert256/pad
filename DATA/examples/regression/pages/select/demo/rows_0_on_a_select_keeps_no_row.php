<?php

  // rows=0 on the tag of a select - or a value there that is no number - keeps no row, as
  // it does on every other data, and a declaration's 'rows' => 0 is no limit: since page=
  // and rows= make the limit when they are written, both were read as the 10 of a rows=
  // not written. A page= beside the declared 0 is a page of 10. Over the staff and the 122
  // customers of the demo database.

  $padSelect ['staffSel']     = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['allCustomers'] = [ 'db' => 'customers', 'key' => 'customerNumber', 'rows' => 0 ];

  $none  = '';
  $names = [ 'ann', 'bea' ];

?>
