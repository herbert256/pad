<?php

  // {form 'contact'} ... {/form}: a form that posts back to the page, carrying the
  // session's CSRF token and its own name - padPosted ( 'contact' ) tells it came back.
  // Any other item is an attribute of the form tag: action=, class=, enctype=, method='get'.
  // The fields inside refill from the post when this form came back (lib/form.php).
  //
  // Runs twice, like {tidy}: first to open the form - the fields rendered inside it must
  // know which form they are in - then on the rendered content, to put the tags round it.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {form} never closes" );

  if ( $padWalk [$pad] == 'start' ) {
    padFormOpen ();
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = padFormClose ( $padContent );

  return TRUE;

?>
