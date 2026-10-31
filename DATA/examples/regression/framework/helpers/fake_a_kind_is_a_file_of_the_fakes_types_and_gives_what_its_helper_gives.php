<?php

  // padFake ( kind ) gives what the kind's helper gives after the same seed, its options by
  // name; the new kinds keep to their shapes - an IBAN whose check digits hold, addresses of
  // the documentation ranges - and the rows of a seed leave the sequence where it was.

  padFakeSeed ( 42 );  $name   = padFakeName ();
  padFakeSeed ( 42 );  $kind   = padFake ( 'name' );
  padFakeSeed ( 5 );   $number = padFakeNumber ( 1, 6 );
  padFakeSeed ( 5 );   $option = padFake ( 'number', [ 'from' => 1, 'to' => 6 ] );

  $shapes = TRUE;

  for ( $i = 0; $i < 50; $i++ ) {

    $iban   = padFake ( 'iban' );
    $digits = '';
    foreach ( str_split ( substr ( $iban, 4 ) . substr ( $iban, 0, 4 ) ) as $char )
      $digits .= ctype_alpha ( $char ) ? (string) ( ord ( $char ) - 55 ) : $char;
    $rest = 0;
    foreach ( str_split ( $digits, 7 ) as $part )
      $rest = (int) ( $rest . $part ) % 97;

    $shapes = ( $shapes and $rest == 1 and preg_match ( '/^NL\d\dTEST\d{10}$/D', $iban )
      and preg_match ( '/^(192\.0\.2|198\.51\.100|203\.0\.113)\.\d{1,3}$/D', padFake ( 'ip' ) )
      and preg_match ( '/^#[0-9a-f]{6}$/D', padFake ( 'colour' ) )
      and preg_match ( '/^\d{5}$/D', padFake ( 'postcode' ) )
      and preg_match ( '/^(0[89]|1[0-7]):[0-5]\d$|^18:00$/D', padFake ( 'time' ) )
      and preg_match ( '#^https://www\.example\.(com|org|net)/[a-z]+(/[a-z]+)?$#D', padFake ( 'url' ) )
      and preg_match ( '/^\d+\.\d\d$/D', padFake ( 'price' ) )
      and preg_match ( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D', padFake ( 'uuid' ) ) );

  }

  $before = padFakeState ();
  padFakeRows ( 'name, email', 4, 7 );
  $after  = padFakeState ();

  $r = json_encode ( [
    'helper'   => $name === $kind,
    'option'   => $number === $option,
    'shapes'   => (bool) $shapes,
    'left'     => $before === $after,
    'same'     => padFakeRows ( 'name, email', 2, 'x' ) === padFakeRows ( [ 'name', 'email' ], 2, 'x' ),
    'kinds'    => count ( array_intersect ( [ 'address', 'email', 'iban', 'name', 'price', 'uuid' ], padFakeKinds () ) ),
  ] );

?>
