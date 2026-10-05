<?php

  // {extends 'layouts/report'} frames the page with a layout instead of its directories'
  // wrappers - resolved while the page is assembled, by lib/layout.php, which takes the tag
  // out. One that reaches this handler is not where the assembly looks for it: directly in
  // a page's own template, outside any other tag.

  if ( $padCheckSyntax )
    padError ( "the {extends} must stand directly in a page's own template, outside any other tag - that is where the page is assembled" );

  return NULL;

?>
