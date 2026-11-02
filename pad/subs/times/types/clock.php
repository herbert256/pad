<?php

  // clock - A timezone-aware world clock, live by default, with a semantic time element.
  // timezone= is an IANA name (default application timezone). now= freezes server output.
  // format= is H:i:s, H:i, Y-m-d H:i:s, Y-m-d H:i, h:i:s A or h:i A. live=0 is static.
  $padTimeZone = padTagParm ( 'timezone', padDateZone ()->getName () );
  $padTimeFormat = padTagParm ( 'format', 'H:i:s' );
  $padTimeNow = padTagParm ( 'now', '' );
  $padTimeMoment = $padTimeNow === '' ? padNow () : padDateParse ( $padTimeNow );
  if ( ! is_string ( $padTimeZone ) || ! in_array ( $padTimeZone, DateTimeZone::listIdentifiers ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) {
    if ( $padCheckSyntax )
      padError ( 'the clock timezone must be an IANA timezone such as Europe/Amsterdam or UTC' );
    return FALSE;
  }
  if ( ! in_array ( $padTimeFormat, [ 'H:i:s', 'H:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'h:i:s A', 'h:i A' ], TRUE ) ) {
    if ( $padCheckSyntax )
      padError ( 'the clock format must be H:i:s, H:i, Y-m-d H:i:s, Y-m-d H:i, h:i:s A or h:i A' );
    return FALSE;
  }
  if ( $padTimeMoment === NULL ) {
    if ( $padCheckSyntax )
      padError ( 'the clock cannot read now as a date' );
    return FALSE;
  }
  return padTimeClock ( $padTimeMoment, $padTimeZone, $padTimeFormat, (bool) padTagParm ( 'live', TRUE ),
                        padParmText ( padTagParm ( 'label', $padTimeZone ) ) );

?>
