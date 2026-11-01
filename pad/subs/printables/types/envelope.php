<?php

  // {printable:envelope data='customers', sender='PAD, Main Street 1, 3511 AB Utrecht'}...{/printable:envelope}
  // - envelopes to print on, one to a sheet the size of the envelope: the content between the
  // tags is the address, set in the right half of its lower part, where the post reads it;
  // sender= the return address top left, its lines between commas. paper= the envelope: dl
  // (220 by 110 mm, the default), c5 (229 by 162), c6 (162 by 114) or c4 (324 by 229); size=
  // (11 pt) the address. The screen shows where the stamp goes; paper and PDF do not.
  //
  //   {printable:envelope data='customers', paper='c5', sender='PAD, Main Street 1, 3511 AB Utrecht'}
  //     {$name}<br>{$street}<br>{$postcode} {$city}
  //   {/printable:envelope}

  $padEnvName   = padPrintableChoice ( 'paper', 'dl', array_keys ( PAD_PRINTABLE_ENVELOPES ) );
  $padEnvPaper  = PAD_PRINTABLE_ENVELOPES [$padEnvName];
  $padEnvSender = padPrintableLines ( 'sender' );
  $padEnvSize   = padPrintableNumber ( 'size', 11, 6, 32 );

  return padPrintablePlan ( [

    'name'      => [ 'envelope', 'envelopes' ],
    'paper'     => $padEnvPaper,
    'paperName' => strtoupper ( $padEnvName ),

    'item' => function ( $cell, $parts ) use ( $padEnvPaper, $padEnvSender, $padEnvSize ) {

      list ( $w, $h ) = $padEnvPaper;

      return [
        $padEnvSender !== '' ? [ 'box', 10, 8, round ( $w * 0.45, 3 ), round ( $h * 0.3, 3 ), $padEnvSender, [ 'pad' => 0, 'size' => 8 ] ] : NULL,
        [ 'box', round ( $w * 0.45, 3 ), round ( $h * 0.45, 3 ), round ( $w * 0.5, 3 ), round ( $h * 0.45, 3 ), $parts [''] ?? '',
          [ 'pad' => 1, 'size' => $padEnvSize ] ],
        [ 'rect', $w - 42, 8, 32, 26, [ 'stroke' => '#c4c3bd', 'width' => 0.6, 'dash' => TRUE, 'screen' => TRUE ] ],
        [ 'box', $w - 42, 8, 32, 26, 'Stamp', [ 'pad' => 1, 'size' => 7, 'align' => 'center', 'valign' => 'middle', 'color' => '#a3a29b', 'screen' => TRUE ] ] ];

    },

  ] );

?>
