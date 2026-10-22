<?php

  // rollup is MySQL's WITH ROLLUP, which SQLite has not got: refused with a message that says
  // so, where the statement ended on SQLite's 'near "with": syntax error'.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name' ];

?>
