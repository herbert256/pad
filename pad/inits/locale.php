<?php

  // The request's locale and timezone, before the page cache keys on them and before any
  // template formats a date. With $padLocales listing the locales the application speaks,
  // padLocaleChoose (lib/locale.php) picks one from ?lang=, the padLang cookie or
  // Accept-Language; without it $padLocale stands as configured. A $padTimezone that PHP
  // does not know is a configuration fault, named as one.

  if ( $padTimezone ) {

    if ( ! in_array ( $padTimezone, timezone_identifiers_list () ) )
      padError ( "there is no timezone named '" . padMakeSafe ( $padTimezone, 40 ) . "'" );
    else
      date_default_timezone_set ( $padTimezone );

  }

  if ( count ( $padLocales ) )
    $padLocale = padLocaleChoose ();

?>
