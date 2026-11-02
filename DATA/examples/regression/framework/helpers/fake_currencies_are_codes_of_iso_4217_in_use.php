<?php

  // padFake ( 'currency' ) is three capitals, a currency ICU's data names - where PHP has
  // intl - and every one of its list, the codes of ISO 4217's list one, comes up; the same
  // seed the same code.

  $names = class_exists ( 'ResourceBundle' ) ? ResourceBundle::create ( 'en', 'ICUDATA-curr' ) : NULL;
  $names = $names ? $names->get ( 'Currencies' ) : NULL;

  padFakeSeed ( 21 );

  $codes = $known = 0;
  $seen  = [];

  for ( $i = 0; $i < 1000; $i++ ) {
    $code     = padFake ( 'currency' );
    $codes   += preg_match ( '/^[A-Z]{3}$/D', $code ) ? 1 : 0;
    $known   += ( ! $names or $names->get ( $code ) ) ? 1 : 0;
    $seen [$code] = TRUE;
  }

  ksort ( $seen );

  padFakeSeed ( 'c' );  $first  = padFake ( 'currency' );
  padFakeSeed ( 'c' );  $second = padFake ( 'currency' );

  $r = json_encode ( [
    'codes' => $codes,
    'known' => $known,
    'all'   => implode ( ' ', array_keys ( $seen ) ),
    'seed'  => $first === $second,
  ] );

?>
