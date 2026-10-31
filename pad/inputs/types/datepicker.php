<?php

  // datepicker - a day typed or chosen from a month that opens beside the field: a native
  // <input type="date">, which posts the day as 2026-10-31 and can be typed into, with a
  // button beside it - where the script runs - that opens a month to choose from. The month is a grid the arrow keys walk, a day a step and a week a row;
  // Page Up and Page Down go a month, with Shift a year; Home and End to the week's ends;
  // Enter takes the day, Escape closes it and gives the focus back to the field. min= and
  // max= - days written 2026-01-31 - hold both the field and the month to them; weekstart=
  // 'sunday' begins the week on Sunday, Monday unless given.

  $padInputBounds = [];

  foreach ( [ 'min', 'max' ] as $padInputEnd ) {
    $padInputDay = padInputTake ( $padInputEnd, NULL );
    if ( $padInputDay === NULL or $padInputDay === '' )
      continue;
    if ( preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', (string) $padInputDay ) and checkdate ( (int) substr ( $padInputDay, 5, 2 ), (int) substr ( $padInputDay, 8, 2 ), (int) substr ( $padInputDay, 0, 4 ) ) )
      $padInputBounds [$padInputEnd] = (string) $padInputDay;
    elseif ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padInputWhat () . " has no usable $padInputEnd '" . padMakeSafe ( (string) $padInputDay, 20 ) . "' - a day written 2026-01-31" );
  }

  $padInputStart = padInputChoice ( 'weekstart', 'monday', [ 'monday', 'sunday' ] );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputBounds, $padInputStart ) {

    global $padInputNow;

    $bounds = '';
    foreach ( $padInputBounds as $end => $day )
      $bounds .= " $end=\"$day\"";

    return padInputFrame ( 'datepicker', $worded,
             $label
           . '<div class="pi-anchor"><div class="pi-row">'
           . "<input type=\"date\" $head value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\"$bounds$rest>"
           . '<button type="button" class="pi-button pi-date-open" aria-haspopup="dialog" aria-expanded="false" aria-label="'
           . padInputEscape ( "Choose $worded" ) . '" hidden>'
           . '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="1.5" y="3" width="13" height="11.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.4"/>'
           . '<path d="M1.5 6.5h13M5 1.5v3M11 1.5v3" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></button>'
           . '</div></div>',
             $error, [ 'weekstart' => $padInputStart ] );

  } );

?>
