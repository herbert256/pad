<?php

  // Implements the print option: appends the {&firstFieldValue} property so every occurrence
  // prints its first field, then applies the formatting options quote, open, glue and close in
  // that order - which is where those four handlers are reached from.
  //
  // A start-phase option, and also included by types/data.php for {data:name print=...}.

  $padContent .= '{&firstFieldValue}';

  if ( padTagParm ('quote') ) include PAD . 'options/quote.php';
  if ( padTagParm ('open')  ) include PAD . 'options/open.php';
  if ( padTagParm ('glue')  ) include PAD . 'options/glue.php';
  if ( padTagParm ('close') ) include PAD . 'options/close.php';

?>