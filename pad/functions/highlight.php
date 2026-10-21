<?php

  // Pipe function highlight('sql'): the value as source code in that language, coloured -
  // {echo $query | highlight('sql')}, {$snippet | highlight('php')}. padHighlight in
  // lib/highlight.php; every piece of the value is escaped, so a field whose last pipe is
  // highlight skips the sanitize chain (level/var.php), as one ending in markdown does.

  // A language it does not know is named as the {highlight} tag names it - the pipe wrote
  // plain text marked data-lang="phpp" in silence.

  if ( isset ( $parm [0] ) and ! padHighlightKnown ( (string) $parm [0] ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "there is no language '" . padMakeSafe ( (string) $parm [0], 20 ) . "' to highlight - pad, php, html, css, js, json, yaml, sql or bash" );

  return padHighlight ( $value, $parm [0] ?? 'text' );

?>
