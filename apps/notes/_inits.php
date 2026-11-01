<?php

  // Every page: the search and the tag asked for, where it runs, and the menu of the frame
  // every PAD application shares (_common/_tags/padFrame.pad) - all notes, a note's own page
  // and the editor being pages of them, the backup, a page per tag, and the two pages behind
  // the backup, the JSON export and the database file, each saying what it is when it is
  // opened as a page. The frame's bar holds the search box and the New note button; each
  // page names itself in $title, the bar's heading.

  $search     = trim ( notesText ( 'q' ) );
  $only       = trim ( notesText ( 'tag' ) );
  $notesWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, no server' : 'on PHP ' . PHP_VERSION;

  $frameMenu = [ [ 'link' => "{$padGo}index",  'label' => 'All notes', 'icon' => 'file-text', 'current' => ( $padPage == 'index' and $only === '' ) or in_array ( $padPage, [ 'note', 'edit' ] ) ],
                 [ 'link' => "{$padGo}backup", 'label' => 'Backup',    'icon' => 'archive',   'current' => $padPage == 'backup' ],
                 [ 'head' => 'Tags' ] ];

  foreach ( notesTags () as $notesTag )
    $frameMenu [] = [ 'link' => "{$padGo}index&tag=" . urlencode ( $notesTag ['tag'] ), 'label' => $notesTag ['tag'], 'icon' => 'hash',
                      'current' => $padPage == 'index' and $only === $notesTag ['tag'] ];

  $frameMenu [] = [ 'head' => 'Behind the backup' ];
  $frameMenu [] = [ 'link' => "{$padGo}export",   'label' => 'Export',   'icon' => 'download', 'current' => $padPage == 'export' ];
  $frameMenu [] = [ 'link' => "{$padGo}database", 'label' => 'Database', 'icon' => 'database', 'current' => $padPage == 'database' ];

  unset ( $notesTag );

?>
