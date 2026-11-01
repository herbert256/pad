<?php

  // A tab just under the 20 000-byte budget with a bar line after every note: 9 900 bars,
  // said in a fraction of a second - where counting the bar lines again for every note took
  // nearly ten. The template keeps only the end of the description.

  $tab = 'e|' . str_repeat ( '0|', 9900 ) . '-';

  $start = hrtime ( TRUE );

?>
