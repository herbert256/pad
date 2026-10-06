<?php

  $v4 = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
  $v7 = '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

  $made = [];

  for ( $i = 0; $i < 500; $i++ )
    $made [] = padStrUuid ( 7 );

  $sorted = $made;
  sort ( $sorted );

  $first = padStrUuid ( '7' );
  $ms    = hexdec ( substr ( str_replace ( '-', '', $first ), 0, 12 ) );

  $r = json_encode ( [
    strlen ( padStrRandom () ),
    strlen ( padStrRandom ( 40 ) ),
    preg_match ( '/^[A-Za-z0-9]{200}$/', padStrRandom ( 200 ) ),
    padStrRandom ( 0 ),
    padStrRandom ( '5' ) !== padStrRandom ( '5' ),
    preg_match ( $v4, padStrUuid () ),
    preg_match ( $v4, padStrUuid ( 4 ) ),
    padStrUuid () !== padStrUuid (),
    preg_match ( $v7, padStrUuid ( 7 ) ),
    preg_match ( $v7, $first ),
    abs ( $ms - (int) ( microtime ( TRUE ) * 1000 ) ) < 5000,
    $sorted === $made,
    count ( array_unique ( $made ) )
  ] );

?>
