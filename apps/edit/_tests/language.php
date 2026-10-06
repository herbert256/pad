<?php

  // The names and documentation behind completion and hover: the built-in names agree with
  // editors/lsp/completions.json, the reference documentation is found, the PHP side knows
  // the documented PAD functions with their real signatures, and an application's own names
  // and a template's fields are found where PAD would find them.

  $builtin = editLanguagePad ();
  $json  = json_decode ( (string) file_get_contents ( editHome () . '/editors/lsp/completions.json' ), TRUE );
  $count = fn ( $detail ) => count ( array_filter ( $json, fn ( $one ) => $one ['detail'] == $detail ) );

  $sameTags      = count ( $builtin ['tags'] ) == $count ( 'PAD tag' ) ? 'yes' : 'no';
  $sameFunctions = count ( $builtin ['functions'] ) == $count ( 'pipe function' ) ? 'yes' : 'no';
  $hasConstructs = in_array ( 'page', $builtin ['constructs'] ) ? 'yes' : 'no';
  $hasActions    = in_array ( 'reverse', $builtin ['actions'] ) ? 'yes' : 'no';

  $ifSummary     = $builtin ['docs'] ['tag'] ['if'] ['summary'] ?? '';
  $upperSummary  = $builtin ['docs'] ['function'] ['upper'] ['summary'] ?? '';
  $pageConstruct = isset ( $builtin ['docs'] ['construct'] ['page'] ) ? 'yes' : 'no';

  $php = editLanguagePhp ();
  $sig = [];

  foreach ( $php ['pad'] as $one )
    $sig [ $one ['name'] ] = $one;

  $arrGet    = $sig ['padArrGet'] ['sig'] ?? '';
  $dbDoc     = $sig ['db'] ['doc'] ?? '';
  $internal  = in_array ( 'str_contains', array_column ( $php ['internal'], 'name' ) ) ? 'yes' : 'no';
  $config    = in_array ( 'padCheckSyntax', array_column ( $php ['config'], 'name' ) ) ? 'yes' : 'no';

  $demo      = editLanguageApp ( 'demo' );
  $demoNames = implode ( ' ', array_map ( fn ( $n ) => $n ['kind'] . ':' . $n ['name'], array_filter ( $demo ['names'], fn ( $n ) => $n ['kind'] != 'lang' ) ) );
  $demoTodo  = in_array ( 'todo', $demo ['pages'] ) ? 'yes' : 'no';

  $fields    = array_unique ( array_column ( editFields ( 'demo', 'todo.pad' ), 'name' ) );
  $todoField = in_array ( 'pendingCount', $fields ) ? 'yes' : 'no';

?>
