<?php

  // Without the strict check an option a kind cannot use takes its default, and what does not
  // fit is left out, without a word: graph paper of a grid that is none is squares, and with
  // no room inside its margins a sheet without a line; a calendar of holidays of no country
  // marks none; a folded card on a paper it does not fold is A4; staff paper of a rastral
  // that is none is rastral 3, and of more staves than fit has those that fit.

  $padCheckSyntax = FALSE;

  $answer = implode ( ' // ', array_map ( fn ( $template ) => printablesShow ( $template, 0 ) . ', '
                                          . preg_match_all ( '/data-pdf-line=/', padUnprotect ( padUnescape ( padCode ( $template ) ) ) ) . ' lines', [
    "{printable:graphpaper grid='hex'/}",
    "{printable:graphpaper paper='a6', margin=60/}",
    "{printable:calendar year=2027, months=1, holidays='xx'/}",
    "{printable:foldcard paper='a5'}Hi{/printable:foldcard}",
    "{printable:staffpaper rastral=12, staves=40/}" ] ) );

?>
