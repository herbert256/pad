<?php

  // What every page's frame shows: the search box, the page's own title, where it runs.

  $notesPage = 'Notes';
  $search    = trim ( notesText ( 'q' ) );
  $notesWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, no server' : 'on PHP ' . PHP_VERSION;

?>
