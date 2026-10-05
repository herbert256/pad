<?php

  // Pipe function localDate(date, time, locale): a date written the locale's way, in the
  // request's timezone - {$created | localDate} is 5 Oct 2026 in en, 5 okt 2026 in nl.
  //
  // date and time are styles - none, short, medium, long, full - with medium and none as the
  // defaults: localDate('long', 'short') is a long date with a short time. A first argument
  // that is no style word is an ICU pattern instead: localDate('d MMMM y'). The value is a
  // Unix timestamp or a date in text; empty is now. Without PHP's intl extension the answer
  // is date('Y-m-d'), with ' H:i' when a time style was asked for.

  $padLdStyles = [ 'none' => -1, 'full' => 0, 'long' => 1, 'medium' => 2, 'short' => 3 ];

  // An empty style is the default one, as an absent one is: localDate('') read the style
  // table at '' - neither a style nor a pattern - and ended on an undefined array key.

  $padLdDate   = strtolower ( trim ( (string) ( $parm [0] ?? '' ) ) ) ?: 'medium';
  $padLdTime   = strtolower ( trim ( (string) ( $parm [1] ?? '' ) ) ) ?: 'none';
  $padLdLocale = padLocaleOf ( $parm [2] ?? '' );
  $padLdStamp  = padLocaleTime ( $value );

  if ( $padLdStamp === NULL )
    return '';

  $padLdPattern = isset ( $padLdStyles [$padLdDate] ) ? '' : (string) ( $parm [0] ?? '' );

  if ( ! isset ( $padLdStyles [$padLdTime] ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "localDate has no time style named '" . padMakeSafe ( $padLdTime, 20 ) . "'" );
    $padLdTime = 'none';
  }

  // A locale intl does not know is a ValueError out of the constructor, which ended the
  // request as an uncaught PHP error in the lenient walk too. Strict mode names it; the
  // lenient walk writes the date as it does without intl.

  if ( class_exists ( 'IntlDateFormatter' ) ) {

    try {
      $padLdFormat = new IntlDateFormatter (
        $padLdLocale,
        $padLdPattern ? IntlDateFormatter::NONE : $padLdStyles [$padLdDate],
        $padLdPattern ? IntlDateFormatter::NONE : $padLdStyles [$padLdTime],
        date_default_timezone_get (),
        IntlDateFormatter::GREGORIAN,
        $padLdPattern ?: NULL
      );
    } catch ( ValueError $padLdError ) {
      $padLdFormat = NULL;
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "localDate: '" . padMakeSafe ( $padLdLocale, 40 ) . "' is not a locale" );
    }

    $padLdText = $padLdFormat ? $padLdFormat->format ( $padLdStamp ) : FALSE;

    if ( $padLdText !== FALSE )
      return $padLdText;

  }

  return date ( ( $padLdTime != 'none' ) ? 'Y-m-d H:i' : 'Y-m-d', $padLdStamp );

?>
