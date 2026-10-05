<?php

  // {spaceless} ... {/spaceless}: the whitespace between HTML tags goes - '<li>a</li>
  // <li>b</li>' becomes '<li>a</li><li>b</li>' - and so does the whitespace at both ends.
  // Text inside a tag keeps its spaces. Like {tidy} it runs twice: first to ask for the end
  // walk, then on the rendered content.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {spaceless} never closes" );

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = trim ( preg_replace ( '/>\s+</', '><', $padContent ) );

  return TRUE;

?>
