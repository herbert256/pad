<?php

  // padNumberOrdinal: st nd rd after 1 2 3, th after the rest and after 11 12 13 - also
  // in 111 and 112 - the sign kept; a whole float and numeric text count as the number.

  $r = json_encode ( array_map ( 'padNumberOrdinal',
    [ 1, 2, 3, 4, 11, 12, 13, 21, 22, 23, 101, 102, 111, 112, 113, 1000000, 0, -1, -11, '3', 3.0, NULL, '' ] ) );

?>
