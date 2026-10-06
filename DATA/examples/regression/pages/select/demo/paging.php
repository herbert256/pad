<?php

  // Paging a declared select table: the page is cut once, by the SQL limit, so page 2 holds
  // the third and fourth rows; a row count in the declaration is a row count, not the
  // query's command word; and a joined table declared without fields joins, adding none.
  // Over the four-row staff table and the join tables of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['staffTwo'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name', 'rows' => 2 ];

  $padSelect ['bare']     = [ 'db' => 'table2', 'key' => 'key' ];
  $padSelect ['joined']   = [ 'db' => 'table1', 'fields' => 'table1.text', 'order' => 'table1.key',
                              'join' => [ 'inner' => 'bare', 'key' => 'table1.key' ] ];

?>
