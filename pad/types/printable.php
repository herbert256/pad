<?php

  // Type handler for a printable: {printable:labels data='products'} ... {/printable:labels}.
  // The kind is the tag's name, set up as {printable 'labels'} sets it up, by
  // subs/printables/printable.php - run as a built-in tag's PHP half is run, types/_go/tag.php,
  // on both walks the printable asks for. A printable is never reached bare: labels, cards,
  // tickets and letter are the names of fields and pages.

  $padPrintableKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/printables/printable';

  return include PAD . 'types/_go/tag.php';

?>
