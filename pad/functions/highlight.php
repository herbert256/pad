<?php

  // Pipe function highlight('sql'): the value as source code in that language, coloured -
  // {echo $query | highlight('sql')}, {$snippet | highlight('php')}. padHighlight in
  // lib/highlight.php; every piece of the value is escaped, so a field whose last pipe is
  // highlight skips the sanitize chain (level/var.php), as one ending in markdown does.

  return padHighlight ( $value, $parm [0] ?? 'text' );

?>
