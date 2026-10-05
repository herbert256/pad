<?php

  // {block 'name'} ... {/block}: a region of a layout or wrapper that a page can override.
  // The blocks are resolved while the page is assembled - lib/layout.php - and gone before
  // it renders; one that reaches this handler stands outside the assembled page, in a
  // snippet or a tag's template, where nothing overrides it: its content renders.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {block} never closes" );

  return TRUE;

?>
