<?php
  $written = padFormatWrite ( 'psv', [ [ 'text' => '=SUM(A1:A2)', 'number' => -4 ], [ 'text' => '+evil', 'number' => 2 ] ] );
  $read = padFormatRead ( 'psv', $written );
  $answer = $read [0] ['text'] === "'=SUM(A1:A2)" && $read [1] ['text'] === "'+evil" && $read [0] ['number'] === '-4'
    && ! isset ( padFormatDef ( 'psv' ) ['sniff'] ) && padFormatDef ( 'psv' ) ['accept'] === []
    ? 'PSV protects formula cells and only loads when explicitly selected' : 'FAIL';
?>
