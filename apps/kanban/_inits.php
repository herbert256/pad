<?php

  // Every page: where it runs, and the menu of the frame every PAD application shares
  // (_common/_tags/padFrame.pad) - the board, a card's own page being a page of it, and the
  // two pages behind it that kanban.js asks, the moves it posts and the events it listens
  // to, each saying what it is when it is opened as a page. The frame's bar holds the Live
  // sign, which kanban.js shows once the event stream is open, and the New card button,
  // which opens the board's dialog; each page names itself in $title, the bar's heading.

  $kanbanWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, the board in IndexedDB' : 'on PHP ' . PHP_VERSION . ', the board in DATA/kanban/kanban.sqlite';

  $frameMenu = [ [ 'link' => "{$padGo}index",  'label' => 'Board',  'icon' => 'columns', 'current' => in_array ( $padPage, [ 'index', 'card' ] ) ],
                 [ 'head' => 'Behind the board' ],
                 [ 'link' => "{$padGo}move",   'label' => 'Moves',  'icon' => 'move',    'current' => $padPage == 'move' ],
                 [ 'link' => "{$padGo}events", 'label' => 'Events', 'icon' => 'rss',     'current' => $padPage == 'events' ] ];

?>
