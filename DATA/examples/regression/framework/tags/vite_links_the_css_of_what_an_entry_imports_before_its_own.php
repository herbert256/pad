<?php

  // An entry importing two chunks that share a third, each with CSS: Vite's build puts the
  // CSS of what a chunk imports before its own, depth first - the entry's own rules last,
  // so they win over a shared chunk's as under the dev server.

  $padViteBuild = 'vitecss';

?>
