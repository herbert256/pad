<?php

  // Fixture for the callback cases: called at each stage of a tag that carries callback= and the
  // before option. With before the whole set is walked first, so a row may be given a field it
  // did not have and a total is finished before the first occurrence renders.

  switch ( $padCallback ) {

    case 'init' : $sum = 0;                                break;
    case 'row'  : $row ['double'] = $row ['n'] * 2;
                  $sum += $row ['n'];                      break;
    case 'exit' : $sum = "[$sum]";                         break;

  }

?>