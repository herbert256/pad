<?php
  $parts = [];
  foreach ( [ 'major' => [ 0, 10 ], 'minor' => [ 0, 20 ], 'patch' => [ 0, 100 ] ] as $name => $default ) {
    $range = padFakeOpt ( $name, $default );
    if ( ! is_array ( $range ) ) $range = [ $range, $range ];
    if ( count ( $range ) !== 2 or ! array_is_list ( $range ) ) {
      padError ( 'fake:semver components are integers or [minimum, maximum] ranges' );
      return NULL;
    }
    foreach ( $range as $value )
      if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or $value < 0 or $value > 1000000000 or floor ( (float) $value ) != $value ) {
        padError ( 'fake:semver component bounds must be integers from 0 to 1000000000' );
        return NULL;
      }
    if ( $range [0] > $range [1] ) { padError ( 'fake:semver inverted component range' ); return NULL; }
    $parts [] = padFakeNumber ( ...$range );
  }
  $text = implode ( '.', $parts );
  foreach ( [ 'prerelease' => '-', 'build' => '+' ] as $key => $separator ) {
    $suffix = padFakeOpt ( $key, '' );
    if ( ! is_string ( $suffix ) or ( $suffix !== '' and ! preg_match ( '/^[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*$/D', $suffix ) ) ) {
      padError ( 'fake:semver suffixes must be dot-separated ASCII identifiers' ); return NULL;
    }
    if ( $key === 'prerelease' )
      foreach ( explode ( '.', $suffix ) as $id )
        if ( strlen ( $id ) > 1 and ctype_digit ( $id ) and $id [0] === '0' ) {
          padError ( 'fake:semver numeric prerelease identifiers cannot have leading zeros' ); return NULL;
        }
    if ( $suffix !== '' ) $text .= $separator . $suffix;
  }
  return $text;
?>
