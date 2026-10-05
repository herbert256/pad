<?php

  // Pipe function currency(code, locale): an amount written as money the locale's way -
  // {$price | currency('EUR')} is € 1.234,56 in nl, €1,234.56 in en. The code defaults to
  // EUR, the locale to the request's $padLocale. Without PHP's intl extension the amount is
  // written with two decimals and the code behind it.

  $padCurCode   = strtoupper ( trim ( (string) ( $parm [0] ?? 'EUR' ) ) ) ?: 'EUR';
  $padCurLocale = padLocaleOf ( $parm [1] ?? '' );
  $padCurAmount = is_numeric ( $value ) ? (float) $value : 0.0;

  if ( ! is_numeric ( $value ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "currency: '" . padMakeSafe ( (string) $value, 40 ) . "' is not an amount" );

  if ( class_exists ( 'NumberFormatter' ) ) {
    $padCurFormat = new NumberFormatter ( $padCurLocale, NumberFormatter::CURRENCY );
    $padCurText   = $padCurFormat->formatCurrency ( $padCurAmount, $padCurCode );
    if ( $padCurText !== FALSE )
      return $padCurText;
  }

  return number_format ( $padCurAmount, 2 ) . " $padCurCode";

?>
