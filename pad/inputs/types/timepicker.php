<?php

  // timepicker - a time of day typed or chosen from the times of a list, every quarter or half
  // hour: a native <input type="time">, which posts 09:30 and can be typed into, with the times
  // to choose from every every= minutes - 30 unless given - from min= to max=, 00:00 to 23:59
  // unless given: a <datalist> the browser offers, and where the script runs a button that
  // opens them as a list the arrow keys walk. step= stays the time
  // field's own: what the field takes, not what the list offers.

  $padInputEvery = padInputNumber ( 'every', 30, 1, 720, TRUE );

  $padInputBounds = [];

  foreach ( [ 'min' => '00:00', 'max' => '23:59' ] as $padInputEnd => $padInputDefault ) {
    $padInputTime = padInputTake ( $padInputEnd, NULL );
    if ( $padInputTime === NULL or $padInputTime === '' )
      $padInputBounds [$padInputEnd] = [ $padInputDefault, FALSE ];
    elseif ( preg_match ( '/^([01]\d|2[0-3]):[0-5]\d$/D', (string) $padInputTime ) )
      $padInputBounds [$padInputEnd] = [ (string) $padInputTime, TRUE ];
    else {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the " . padInputWhat () . " has no usable $padInputEnd '" . padMakeSafe ( (string) $padInputTime, 20 ) . "' - a time written 09:30" );
      $padInputBounds [$padInputEnd] = [ $padInputDefault, FALSE ];
    }
  }

  $padInputSlots = [];
  $padInputFrom  = (int) substr ( $padInputBounds ['min'] [0], 0, 2 ) * 60 + (int) substr ( $padInputBounds ['min'] [0], 3, 2 );
  $padInputTo    = (int) substr ( $padInputBounds ['max'] [0], 0, 2 ) * 60 + (int) substr ( $padInputBounds ['max'] [0], 3, 2 );

  for ( $padInputAt = $padInputFrom; $padInputAt <= $padInputTo and count ( $padInputSlots ) < 1440; $padInputAt += $padInputEvery )
    $padInputSlots [] = sprintf ( '%02d:%02d', intdiv ( $padInputAt, 60 ), $padInputAt % 60 );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputBounds, $padInputSlots ) {

    global $padInputNow;

    $bounds = '';
    foreach ( $padInputBounds as $end => [ $time, $given ] )
      if ( $given )
        $bounds .= " $end=\"$time\"";

    $list = '<datalist id="' . padInputEscape ( "$id-times" ) . '">';
    foreach ( $padInputSlots as $slot )
      $list .= "<option value=\"$slot\"></option>";
    $list .= '</datalist>';

    return padInputFrame ( 'timepicker', $worded,
             $label
           . '<div class="pi-anchor"><div class="pi-row">'
           . "<input type=\"time\" $head value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\" list=\"" . padInputEscape ( "$id-times" ) . "\"$bounds$rest>"
           . '<button type="button" class="pi-button pi-time-open" aria-haspopup="listbox" aria-expanded="false" aria-label="'
           . padInputEscape ( "Choose $worded" ) . '" hidden>'
           . '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><circle cx="8" cy="8" r="6.5" fill="none" stroke="currentColor" stroke-width="1.4"/>'
           . '<path d="M8 4.2V8l2.6 1.6" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round"/></svg></button>'
           . '</div></div>' . $list,
             $error, [ 'slots' => $padInputSlots ] );

  } );

?>
