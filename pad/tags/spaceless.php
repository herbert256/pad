<?php

  // {spaceless} ... {/spaceless}: the whitespace between HTML tags goes - '<li>a</li>
  // <li>b</li>' becomes '<li>a</li><li>b</li>' - and so does the whitespace at both ends.
  // Text inside a tag keeps its spaces, and so does what a <pre>, <textarea>, <script> or
  // <style> holds. Like {tidy} it runs twice: first to ask for the end
  // walk, then on the rendered content.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {spaceless} never closes" );

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  // What a <pre>, <textarea>, <script> or <style> holds is text as it stands: the white
  // space between two tags in a <pre> is part of what it shows - a {highlight} sample came
  // out on one line with its tokens glued together, $a=1;echo$a; - and a textarea of
  // blanks lost its value. Such an element is matched whole up to the > of its close, which
  // is left for the white space after it.

  $padContent = trim ( preg_replace_callback (
    '#<(pre|textarea|script|style)(?=[\s/>]).*?</\1\s*(?=>)|>\s+(?=<)#is',
    fn ( $match ) => isset ( $match [1] ) ? $match [0] : '>',
    $padContent
  ) ?? $padContent );

  return TRUE;

?>
