<?php

  // {printable 'labels', data='products', sheet='a4-3x8'} ... {/printable} - a page made for
  // paper of the kind its parameter names: the content between the tags is the template of
  // one item - a label, a badge, a ticket, a letter - repeated over the data and laid out on
  // sheets of paper. A kind is a file of subs/printables/types/, laid out by the printables
  // subsystem in subs/printables/ - the same printable as the printable: type writes it,
  // {printable:labels ...} ... {/printable:labels}.

  $padPrintableKind = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';

  return include PAD . 'subs/printables/printable.php';

?>
