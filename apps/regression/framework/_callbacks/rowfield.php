<?php

  // Fixture for the streaming callback cases: without before, the row phase reads the row's
  // field as a plain variable and adds it up - in a pass that runs inside a PHP function, a
  // | code pipe or a _data file, as much as on the page itself.

  switch ( $padCallback ) {

    case 'init' : $sumRows = 0;             break;
    case 'row'  : $sumRows += $n;           break;
    case 'exit' : $sumRows = "[$sumRows]";  break;

  }

?>
