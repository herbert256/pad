<?php

  // An escaped quote inside a {meta} value ends neither the value nor the item: the items
  // after it are read, and the file's own literals - padMeta for another page - come back
  // unescaped, as the expression evaluator reads them.

  $fromFile = padMeta ( 'title', 'build/meta_reads_an_escaped_quote_inside_a_value' ) . '|'
            . padMeta ( 'sub',   'build/meta_reads_an_escaped_quote_inside_a_value' );

?>
