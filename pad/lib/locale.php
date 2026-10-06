<?php

  // The locale: one setting - $padLocale, with $padTimezone beside it - that {trans}, the
  // trans, currency and localDate functions and padTrans() all read.
  //
  // padLocaleChoose      picks the request's locale from $padLocales, the ones the
  //                      application speaks: a ?lang= choice (kept in the padLang cookie),
  //                      else the cookie, else the best Accept-Language match, else
  //                      $padLocale. inits/locale.php runs it when the list is not empty.
  // padLocaleCandidates  'nl_NL' as the names to look for, most specific first: nl_NL,
  //                      nl-NL, nl
  // padTrans             a key looked up in the _lang/ catalogs, plural form and
  //                      placeholders applied - the work behind {trans} and | trans
  //
  // A catalog is _lang/<locale>.json - { "cart.items": "%d item|%d items" } - looked up the
  // way _data/ files are: from the page's directory up to the application root, then the
  // _common application. A key no catalog knows is answered as itself.

  function padLocaleNormal ( $locale ) {

    $locale = str_replace ( '-', '_', trim ( (string) $locale ) );

    if ( preg_match ( '/^([a-zA-Z]{2,3})(?:_([a-zA-Z0-9]{2,4}))?$/', $locale, $match ) )
      return strtolower ( $match [1] ) . ( isset ( $match [2] ) ? '_' . strtoupper ( $match [2] ) : '' );

    return '';

  }

  function padLocaleCandidates ( $locale ) {

    $locale = padLocaleNormal ( $locale );

    if ( $locale === '' )
      return [];

    $list = [ $locale ];

    if ( str_contains ( $locale, '_' ) ) {
      $list [] = str_replace ( '_', '-', $locale );
      $list [] = substr ( $locale, 0, strpos ( $locale, '_' ) );
    }

    return $list;

  }

  // The application's locale out of a list, matched on the whole tag first and on the
  // language alone after that: a visitor asking for nl-BE gets nl or nl_NL.

  function padLocaleMatch ( $wanted, $locales ) {

    $wanted = padLocaleNormal ( $wanted );

    if ( $wanted === '' )
      return '';

    foreach ( $locales as $one )
      if ( padLocaleNormal ( $one ) === $wanted )
        return $one;

    $language = explode ( '_', $wanted ) [0];

    foreach ( $locales as $one )
      if ( explode ( '_', padLocaleNormal ( $one ) ) [0] === $language )
        return $one;

    return '';

  }

  function padLocaleChoose () {

    global $padLocale, $padLocales, $padCookies;

    if ( isset ( $_GET ['lang'] ) and is_string ( $_GET ['lang'] ) ) {

      $chosen = padLocaleMatch ( $_GET ['lang'], $padLocales );

      if ( $chosen !== '' ) {

        if ( $padCookies and ! headers_sent () )
          setcookie ( 'padLang', $chosen, [ 'expires' => time () + 60 * 60 * 24 * 366,
                                            'httponly' => TRUE, 'samesite' => 'Lax', 'path' => '/' ] );

        return $chosen;

      }

    }

    if ( isset ( $_COOKIE ['padLang'] ) and is_string ( $_COOKIE ['padLang'] ) ) {
      $chosen = padLocaleMatch ( $_COOKIE ['padLang'], $padLocales );
      if ( $chosen !== '' )
        return $chosen;
    }

    $accept = [];

    foreach ( explode ( ',', $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ?? '' ) as $n => $part ) {
      $bits = explode ( ';', trim ( $part ) );
      $q    = 1.0;
      foreach ( array_slice ( $bits, 1 ) as $bit )
        if ( preg_match ( '/^\s*q\s*=\s*([0-9.]+)/', $bit, $match ) )
          $q = (float) $match [1];
      if ( trim ( $bits [0] ) !== '' and $q > 0 )
        $accept [] = [ $q, -$n, trim ( $bits [0] ) ];
    }

    rsort ( $accept );

    foreach ( $accept as [ $q, $n, $tag ] ) {
      $chosen = padLocaleMatch ( $tag, $padLocales );
      if ( $chosen !== '' )
        return $chosen;
    }

    return padLocaleMatch ( $padLocale, $padLocales ) ?: ( $padLocales [0] ?? $padLocale );

  }

  // The catalogs for the current locale, merged so the most specific wins: a page directory's
  // _lang/ over the root's, the root's over _common's, nl_NL over nl.

  function padTransCatalog () {

    global $padLocale, $padTransCatalogs, $padCommon;

    $locale = (string) $padLocale;

    // Kept per locale and per directory of the page: a {page} from a directory with a
    // _lang/ of its own reads that one - kept per locale alone, it was given the catalog of
    // the first page that asked.

    $key = $locale . '|' . APP . '|' . ( $GLOBALS ['padDir'] ?? '' );

    if ( isset ( $padTransCatalogs [$key] ) )
      return $padTransCatalogs [$key];

    $dirs = [];

    if ( $padCommon )
      $dirs [] = COMMON . '_lang/';

    foreach ( array_reverse ( padDirs () ) as $value )
      $dirs [] = APP2 . $value . '_lang/';

    $catalog = [];

    foreach ( $dirs as $dir )
      foreach ( array_reverse ( padLocaleCandidates ( $locale ) ) as $name ) {
        $file = "$dir$name.json";
        if ( is_file ( $file ) ) {
          $more = json_decode ( (string) file_get_contents ( $file ), TRUE );
          // array_replace, not array_merge: a key that is a number - "404", "2024" - is an
          // integer key once decoded, and array_merge numbered those again from 0, so the
          // key was lost and the more specific catalog never replaced it.
          if ( is_array ( $more ) )
            $catalog = array_replace ( $catalog, $more );
          elseif ( $GLOBALS ['padCheckSyntax'] )
            padError ( "the catalog $file is not valid JSON" );
        }
      }

    return $padTransCatalogs [$key] = $catalog;

  }

  // A key in the current locale. $vars holds the substitutions: count picks the plural form
  // and replaces %d, and every name replaces :name. Plural forms are separated by |: two
  // forms are one and other, three are zero, one and other.

  function padTrans ( $key, $vars = [] ) {

    $catalog = padTransCatalog ();
    $text    = $catalog [$key] ?? $key;

    if ( ! is_string ( $text ) )
      $text = is_scalar ( $text ) ? (string) $text : $key;

    if ( array_key_exists ( 'count', $vars ) and str_contains ( $text, '|' ) ) {

      $forms = explode ( '|', $text );
      $count = (float) $vars ['count'];

      if ( count ( $forms ) >= 3 )
        $text = ( $count == 0 ) ? $forms [0] : ( ( $count == 1 ) ? $forms [1] : $forms [2] );
      else
        $text = ( $count == 1 ) ? $forms [0] : $forms [1];

    }

    // The placeholders are filled in one pass over the text as it was written, the longest
    // name first where two start alike (:count before :co). One after the other, a value
    // that held a placeholder was filled in turn: name=':place' came out as the place.

    $fill = [];

    if ( array_key_exists ( 'count', $vars ) )
      $fill ['%d'] = (string) $vars ['count'];

    foreach ( $vars as $name => $value )
      if ( is_scalar ( $value ) )
        $fill [":$name"] = (string) $value;

    return strtr ( $text, $fill );

  }

  // The locale a function was handed, or the request's.

  function padLocaleOf ( $given ) {

    $given = trim ( (string) $given );

    return ( $given !== '' ) ? $given : (string) $GLOBALS ['padLocale'];

  }

  // A date value as a timestamp: a number as it is, text through strtotime, empty as now.

  function padLocaleTime ( $value ) {

    if ( $value === '' or $value === NULL or $value === 0 or $value === '0' )
      return time ();

    if ( is_numeric ( $value ) )
      return (int) $value;

    $time = strtotime ( (string) $value );

    if ( $time === FALSE and $GLOBALS ['padCheckSyntax'] )
      padError ( "localDate cannot read '" . padMakeSafe ( (string) $value, 40 ) . "' as a date" );

    return ( $time === FALSE ) ? NULL : $time;

  }

?>
