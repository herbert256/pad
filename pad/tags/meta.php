<?php

  // {meta title='Monthly report', layout='_layouts/print', cache=600}: the page's metadata,
  // read while the page is assembled and taken out of the text - lib/meta.php. One that
  // reaches this handler is not where the assembly looks: directly in a page's own
  // template, outside any other tag.

  if ( $padCheckSyntax )
    padError ( "the {meta} must stand directly in a page's own template, outside any other tag - that is where the page is assembled" );

  return NULL;

?>
