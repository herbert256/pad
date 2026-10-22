<?php

  // The tables as the database has them - each column, the ones of the key marked - beside
  // the declarations of _lib/select.php that make them tags.

  $columns = db ( "array table_name as tbl, column_name as col, column_key as pk, column_type as type
                     from information_schema.columns
                    where table_schema = database()
                    order by table_name, ordinal_position" );

  $tables = [];

  foreach ( $columns as $one ) {

    $name = strtolower ( $one ['tbl'] );

    $tables [$name] ['table']     ??= $name;
    $tables [$name] ['rows']      ??= db ( "field count(*) from `$name`" );
    $tables [$name] ['columns'] [] = [ 'column' => $one ['col'], 'type' => $one ['type'], 'key' => $one ['pk'] == 'PRI' ];

  }

  $tables      = array_values ( $tables );
  $modelSource = trim ( preg_replace ( '/\A<\?php\R|\?>\s*\z/', '', file_get_contents ( APP . '_lib/select.php' ) ), "\r\n" );
  $modelHtml   = padHighlightTokens ( preg_replace ( '/^  /m', '', $modelSource ), 'php' );

?>
