<?php

  // Entry point of calendar:kind, dispatched to the requested view.
  include_once PAD . 'subs/calendars/_lib/calendar.php';
  include_once PAD . 'subs/calendars/_lib/views.php';

  if ( ! padKind ( 'calendars', $padCalendarKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no calendar type named '" . padMakeSafe ( $padCalendarKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'calendars' ) ) );
    return FALSE;
  }

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'calendar', $padCalendarKind );

  return include PAD . "subs/calendars/types/$padCalendarKind.php";

?>
