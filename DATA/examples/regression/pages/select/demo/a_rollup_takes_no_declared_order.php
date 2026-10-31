<?php

  // MariaDB, and MySQL before 8.0.12, take no ORDER BY beside WITH ROLLUP, whose rows come in
  // the order of the group: a rollup - on the tag or declared - writes only an order= given
  // on the tag, not the order of a declaration that groups, which ended the select on
  // "Incorrect usage of CUBE/ROLLUP and ORDER BY". Over the staff table of the demo database.

  $padSelect ['bands']       = [ 'db' => 'staff', 'fields' => 'floor(salary / 2000) as band, count(*) as n',
                                 'group' => 'band', 'order' => 'band desc' ];
  $padSelect ['bandsRollup'] = [ 'base' => 'bands', 'rollup' => TRUE ];

?>
