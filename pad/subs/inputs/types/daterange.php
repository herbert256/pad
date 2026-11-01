<?php

  // daterange - a first and a last day chosen as one, a stay or a leave: two native <input
  // type="date">, one for each end, under a legend - two fields of the form, {daterange 'stay'}
  // posting stay_from and stay_to as 2026-06-01, names= two others - typed into as they are,
  // and where the script runs a button that opens one month to choose both from, the WAI-ARIA
  // date picker dialog: the arrow keys walk the days, Page Up and Page Down the months (with
  // Shift the years), Home and End the week; Enter or a click takes the first day, then the
  // last, the days between marked as the pointer or the focus moves - a day before the first
  // starts again from there - and Escape closes it. Both days are the range's own: the last
  // is in it. nights counts the nights between them - a hotel's arrival and departure, the
  // last after the first - where the days are counted unless given, the count shown beside
  // the fields. min= and max= - days written 2026-01-31 - hold both fields and the month to
  // them, weekstart= 'sunday' begins the week on Sunday; labels= what each end says, 'From,
  // To' unless given; value= the two before a post. A range is two fields and has no rules= -
  // the page's PHP checks the two with padValidate, after:stay_from on the last for nights.

  include_once PAD . 'subs/inputs/_lib/pair.php';

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

  $padInputStart  = padInputChoice ( 'weekstart', 'monday', [ 'monday', 'sunday' ] );
  $padInputNights = padInputFlag ( 'nights' );

  return padInputPair ( function ( $ends, $legend, $worded ) use ( $padInputBounds, $padInputStart, $padInputNights ) {

    $bounds = '';
    foreach ( $padInputBounds as $end => $day )
      $bounds .= " $end=\"$day\"";

    $fields = '';
    $errors = '';

    foreach ( $ends as $at => $end ) {
      $fields .= '<div class="pi-dr-end pi-dr-' . ( $at ? 'to' : 'from' ) . '">' . $end ['label']
               . "<input type=\"date\" $end[head] value=\"" . padInputEscape ( $end ['value'] ) . "\"$bounds$end[rest]></div>";
      $errors .= $end ['error'];
    }

    return padInputFrame ( 'daterange', $worded,
             "<legend>$legend</legend>"
           . '<div class="pi-anchor"><div class="pi-row pi-dr-row">' . $fields
           . '<button type="button" class="pi-button pi-dr-open" aria-haspopup="dialog" aria-expanded="false" aria-label="'
           . padInputEscape ( "Choose $worded" ) . '" hidden>'
           . '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="1.5" y="3" width="13" height="11.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.4"/>'
           . '<path d="M1.5 6.5h13M5 1.5v3M11 1.5v3" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></button>'
           . '<output class="pi-dr-count pi-muted" aria-live="polite"></output>'
           . '</div></div>',
             $errors, [ 'weekstart' => $padInputStart, 'nights' => $padInputNights ], 'fieldset' );

  } );

?>
