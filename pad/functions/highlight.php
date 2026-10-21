<?php

  // Pipe function highlight('sql'): the value as source code in that language, coloured -
  // {echo $query | highlight('sql')}, {$snippet | highlight('php')}. padHighlight in
  // lib/highlight.php; every piece of the value is escaped, so a field whose last pipe is
  // highlight skips the sanitize chain (level/var.php), as one ending in markdown does.

  // A language it does not know is named as the {highlight} tag names it - the pipe wrote
  // plain text marked data-lang="phpp" in silence.

  // An empty one - a language column left empty, highlight($lang) with $lang '' - is plain
  // text, as the tag takes it.

  $padHighlightLang = trim ( (string) ( $parm [0] ?? '' ) );

  if ( $padHighlightLang === '' )
    $padHighlightLang = 'text';

  if ( ! padHighlightKnown ( $padHighlightLang ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "there is no language '" . padMakeSafe ( $padHighlightLang, 20 ) . "' to highlight - pad, php, html, css, js, json, yaml, sql or bash" );

  return padHighlight ( $value, $padHighlightLang );

?>
