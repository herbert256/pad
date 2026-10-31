<?php

  // The order a table declares - or its key - sorts the rows of the table, not the rows a
  // select makes of them: a rollup, a total over the whole table, one value. Under
  // ONLY_FULL_GROUP_BY, as MySQL has it by default, an order by a column the total does not
  // hold is an error; with a rollup it is one on MariaDB as well. A view that groups keeps an
  // order of its own. Over the four-row staff table of the demo database.

  db ( "set session sql_mode = concat(@@sql_mode, ',ONLY_FULL_GROUP_BY')" );

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];
  $padSelect ['staffKey'] = [ 'db' => 'staff', 'key' => 'name' ];

  $padSelect ['perBand']  = [ 'base' => 'staffSel', 'fields' => 'floor(salary / 2000) as band, sum(bonus) as bonus',
                              'group' => 'band', 'rollup' => TRUE ];
  $padSelect ['bandDesc'] = [ 'base' => 'staffSel', 'fields' => 'floor(salary / 2000) as band, count(*) as n',
                              'group' => 'band', 'order' => 'band desc' ];

?>
