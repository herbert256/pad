<?php

  // An answer of 77 KB that changed at byte 70000: cut from the start of each body, the row
  // kept two equal texts and the difference was nowhere. Both are kept from 4 KB before the
  // place they part, with a note of what was left out in front of it. A body alone is cut
  // from its start, as before.

  $fwWant = str_repeat ( "<p>row</p>\n", 7000 );
  $fwGot  = substr_replace ( $fwWant, 'X', 70000, 1 );

  $fwShownWant = getSuiteShown ( $fwWant, $fwGot  );
  $fwShownGot  = getSuiteShown ( $fwGot,  $fwWant );

  $fwAt = strspn ( $fwShownWant ^ $fwShownGot, "\0" );

  $result = ( $fwShownWant === $fwShownGot ? 'same' : 'differ' ) . ' '
          . ( str_starts_with ( $fwShownWant, "... 65904 bytes before\n" ) && str_starts_with ( $fwShownGot, "... 65904 bytes before\n" ) ? 'noted' : 'unnoted' ) . ' '
          . ( substr ( $fwShownGot, $fwAt, 1 ) == 'X' ? 'at-the-change' : 'elsewhere' ) . ' '
          . ( str_ends_with ( getSuiteShown ( $fwWant ), "\n... cut - 77000 bytes in all" ) ? 'alone-from-the-start' : 'alone-otherwise' );

?>
