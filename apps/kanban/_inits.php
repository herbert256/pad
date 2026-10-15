<?php

  // Every page: its title, and where it runs.

  $kanbanPage  = 'Board';
  $kanbanWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, the board in IndexedDB' : 'on PHP ' . PHP_VERSION . ', the board in DATA/kanban/kanban.sqlite';

?>
