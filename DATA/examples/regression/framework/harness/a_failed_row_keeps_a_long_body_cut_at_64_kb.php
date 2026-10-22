<?php

  // A failed row of a suite result keeps want and got as the overview renders them - and a
  // page that died on PHP's time limit came back as a 127 MB error report, which the
  // framework overview then ran out of its gigabyte showing. A body over 64 KB is kept cut,
  // at a character - é is two bytes - and says how long it was; a shorter one is kept whole.

  $fwShown = getSuiteShown ( str_repeat ( 'é', 40000 ) . '{x}' );

  $result = ( mb_check_encoding ( $fwShown, 'UTF-8' ) ? 'utf8' : 'broken' ) . ' ' . strlen ( $fwShown ) . ' '
          . ( str_ends_with ( $fwShown, "\n... cut - 80003 bytes in all" ) ? 'said' : 'unsaid' ) . ' '
          . ( getSuiteShown ( '<a>{x}' ) === padEscape ( htmlspecialchars ( '<a>{x}' ) ) ? 'short-whole' : 'short-changed' );

?>
