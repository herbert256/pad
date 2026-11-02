<?php

  padFakeSeed ( 5 );
  padFakeNumber ( 1, 9 );
  $before = padFakeNumber ( 1, 1000000 );

  padFakeSeed ( 5 );
  padFakeNumber ( 1, 9 );
  padPattern ( 'circles', [ 'seed' => 3 ] );
  $after = padFakeNumber ( 1, 1000000 );

?>
