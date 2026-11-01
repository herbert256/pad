<?php

  // clocks - the time three ways at once: a clock face with a sweeping second hand, a binary
  // clock whose columns of lights are the digits of the hours, minutes and seconds, and a word
  // clock that lights up the words - it is twenty past four. Drag the minute hand to set the
  // clocks to another time, run them fast to watch a day go by, Now puts them right. show=
  // which of them, 'analog, binary, words' unless given; zone= the time zone they tell, the
  // visitor's own unless given - Europe/Amsterdam, America/New_York ...
  //
  //   {clocks show='analog, words', zone='Asia/Tokyo'/}

  $padClockShow = [];

  foreach ( explode ( ',', strtolower ( padParmText ( padTagParm ( 'show', 'analog, binary, words' ) ) ) ) as $padClockOne )
    if ( in_array ( $padClockOne = trim ( $padClockOne ), [ 'analog', 'binary', 'words' ], TRUE ) )
      $padClockShow [$padClockOne] = TRUE;
    elseif ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no clock '" . padMakeSafe ( $padClockOne, 20 ) . "' to show - analog, binary, words" );

  // A zone is a name of the time zone database, the backward-compatible ones among them -
  // GMT, Etc/GMT+5, US/Eastern, Asia/Calcutta - which the browser's Intl.DateTimeFormat the
  // script hands it to knows as well.

  $padClockZone = trim ( padParmText ( padTagParm ( 'zone', '' ) ) );

  if ( $padClockZone !== '' and ! in_array ( $padClockZone, timezone_identifiers_list ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no time zone '" . padMakeSafe ( $padClockZone, 40 ) . "' - one like Europe/Amsterdam or UTC" );
    $padClockZone = '';
  }

  return padToy ( 'clocks', 'Clocks', [
    'show' => array_keys ( $padClockShow ?: [ 'analog' => TRUE, 'binary' => TRUE, 'words' => TRUE ] ),
    'zone' => $padClockZone
  ] );

?>
