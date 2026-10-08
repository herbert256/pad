<?php

  mt_srand ( 5 );
  $before = mt_rand ();

  mt_srand ( 5 );
  padFakeSeed ( 42 );
  $one = [ padFakeName (), padFakeNumber ( 1, 1000 ), padFakeWord () ];
  $after = mt_rand ();

  padFakeSeed ( 42 );
  $two = [ padFakeName (), padFakeNumber ( 1, 1000 ), padFakeWord () ];

  padFakeSeed ( 'forty-two' );
  $text = padFakeName ();

  padFakeSeed ( 'forty-two' );
  $textAgain = padFakeName ();

  padFakeSeed ( 43 );
  $other = [ padFakeName (), padFakeNumber ( 1, 1000 ), padFakeWord () ];

  $r = json_encode ( [
    'same'       => $one === $two,
    'values'     => $one,
    'mtRandSame' => $before === $after,
    'textSeed'   => $text === $textAgain,
    'otherSeed'  => $other !== $one,
  ] );

?>
