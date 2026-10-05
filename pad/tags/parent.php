<?php

  // {parent} inside a {block} that overrides another is the content it overrides - put in
  // place while the page is assembled, by lib/layout.php. One that reaches this handler
  // stands outside such a block.

  if ( $padCheckSyntax )
    padError ( "the {parent} stands outside a block that overrides another" );

  return NULL;

?>
