<?php

  // Feature flags: a part of the application switched on or off without a deploy, or
  // shown to a share of the visitors first - Laravel's Pennant, Rails' Flipper. The flags
  // are the setting $padFeatures, name => what decides it:
  //
  //   $padFeatures = [
  //     'newCheckout' => TRUE,              // on for everybody
  //     'darkMode'    => FALSE,             // off
  //     'search2'     => 0.25,              // on for a quarter of the visitors
  //     'beta'        => 'isBetaTester',    // a function of the application, given the user
  //   ];
  //
  // padFeature          whether a flag is on for this visitor
  // padFeatureOverride  fixes a flag to TRUE or FALSE for the rest of the request - a test,
  //                     a preview; NULL takes the fix away
  // padFeatureId        the stable id the shares are decided by, or - given one - the id to
  //                     decide them by for the rest of the request
  //
  // padFeatureList / padFeatureShare / padFeatureVisitor are the private helpers below.
  //
  // A share is decided per visitor and stays decided: a hash of the flag's name and the
  // visitor's id, read as a number from 0 to 1, is below the share or not. The id is the
  // logged-in user's (lib/auth.php), so a user gets the same answer on every device; for a
  // guest a random id in the long-lived padFeatureId cookie, made the first time a share is
  // asked. Raising a share keeps everyone who had the flag and adds some; the hash takes
  // the name along, so two flags of 0.5 do not pick the same half. A guest who logs in is
  // decided by the user's id from then on, and may see a flag change once.
  //
  // A name that is no flag is FALSE, and under the strict check an error - a misspelled
  // flag in a template stayed off and nobody knew. {feature 'name'}...{else}...{/feature}
  // asks from a template.

  const padFeatureCookie = 'padFeatureId';

  function padFeature ( $name ) {

    global $padCheckSyntax, $padFeatures;

    if ( ! is_string ( $name ) or $name === '' ) {
      padError ( 'padFeature: a flag is a name, not ' . padRequestShow ( $name ) );
      return FALSE;
    }

    $list = padFeatureList ();

    if ( array_key_exists ( $name, $list ) )
      return $list [$name];

    $flags = is_array ( $padFeatures ?? NULL ) ? $padFeatures : [];

    if ( ! array_key_exists ( $name, $flags ) ) {
      if ( $padCheckSyntax )
        padError ( "padFeature: there is no flag named '$name' in \$padFeatures" );
      return FALSE;
    }

    $flag = $flags [$name];

    if ( is_bool ( $flag ) )
      return $flag;

    if ( is_int ( $flag ) or is_float ( $flag ) ) {

      if ( ! is_finite ( (float) $flag ) or $flag < 0 or $flag > 1 ) {
        padError ( "padFeature: the share of '$name' must be between 0 and 1, not $flag" );
        return FALSE;
      }

      return padFeatureShare ( $name, (float) $flag, padFeatureId () );

    }

    if ( $flag instanceof Closure or ( is_string ( $flag ) and function_exists ( $flag ) ) )
      return (bool) $flag ( padUser () );

    padError ( "padFeature: the flag '$name' must be TRUE, FALSE, a share from 0 to 1 or the name of a function, not "
             . padRequestShow ( $flag ) );

    return FALSE;

  }

  function padFeatureOverride ( $name, $value ) {

    if ( ! is_string ( $name ) or $name === '' ) {
      padError ( 'padFeatureOverride: a flag is a name, not ' . padRequestShow ( $name ) );
      return FALSE;
    }

    if ( $value !== NULL and ! is_bool ( $value ) ) {
      padError ( "padFeatureOverride: the flag '$name' is fixed to TRUE or FALSE, or freed with NULL, not " . padRequestShow ( $value ) );
      return FALSE;
    }

    $list = &padFeatureList ();

    if ( $value === NULL )
      unset ( $list [$name] );
    else
      $list [$name] = $value;

    return TRUE;

  }

  // The id is asked once per request and kept: the user's, the cookie's, or a new one.

  function padFeatureId ( $id = NULL ) {

    static $now = NULL;

    if ( $id !== NULL ) {

      if ( ! is_string ( $id ) and ! is_int ( $id ) ) {
        padError ( 'padFeatureId: an id is a text or a number, not ' . get_debug_type ( $id ) );
        return (string) $now;
      }

      return $now = (string) $id;

    }

    return $now ??= padFeatureVisitor ();

  }

  // ------------------------------------------------------------------------------------
  // The private helpers.
  // ------------------------------------------------------------------------------------

  function &padFeatureList () {

    static $list = [];

    return $list;

  }

  // The first eight bytes of the hash as a number from 0 up to 1.

  function padFeatureShare ( $name, $share, $id ) {

    if ( $share <= 0 ) return FALSE;
    if ( $share >= 1 ) return TRUE;

    return hexdec ( substr ( hash ( 'sha256', "$name\n$id" ), 0, 8 ) ) / 4294967296 < $share;

  }

  // The visitor: the user logged in, else the cookie - 32 hex characters, anything else is
  // made anew - set for five years. When the headers have gone out the new id lives for
  // this request only.

  function padFeatureVisitor () {

    $user = padUserId ();

    if ( $user !== NULL )
      return "user:$user";

    $id = $_COOKIE [padFeatureCookie] ?? '';

    if ( ! is_string ( $id ) or ! preg_match ( '/^[0-9a-f]{32}$/D', $id ) ) {

      $id = bin2hex ( random_bytes ( 16 ) );

      $_COOKIE [padFeatureCookie] = $id;

      if ( ! headers_sent () )
        setcookie ( padFeatureCookie, $id, [
          'expires'  => time () + 5 * 365 * 86400,
          'path'     => '/',
          'httponly' => TRUE,
          'samesite' => 'Lax',
          'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
        ] );

    }

    return "visitor:$id";

  }

?>
