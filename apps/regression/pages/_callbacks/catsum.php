<?php

  // Fixture for the catalog function line: totals the rows into $t, which the @function
  // group reads back from the level the callback ran for.

  switch ( $padCallback ) {

    case 'init' : $t = 0;             break;
    case 'row'  : $t += $row ['cFn']; break;

  }

?>