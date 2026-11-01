<?php

  // Markdown pipe tables (GitHub Flavored Markdown), the reader and the writer of
  // types/markdown.php.
  //
  // The reader takes the first table of the text - the text around it, a heading, a
  // paragraph, may stand there: a header row with pipes, the row of dashes under it, then a
  // row per line up to a blank line or a line without a pipe. The pipes at the ends are
  // optional; \| is a pipe inside a cell and <br> a line break in one. A cell is its text -
  // the Markdown in it is not rendered - and a row short of the header has the rest empty.
  //
  // The writer lines the columns up: a header, a row of dashes - ending in : over a column of
  // numbers, which lines them up on the right - and a row per row, every cell padded to its
  // column's width, a | in a cell written \| and a newline <br>.

  function padMarkdownRead ( $text ) {

    $lines = explode ( "\n", str_replace ( "\r\n", "\n", $text ) );
    $count = count ( $lines );
    $head  = NULL;

    for ( $at = 0; $at < $count - 1; $at++ )
      if ( str_contains ( $lines [$at], '|' ) and padMarkdownDashes ( $lines [ $at + 1 ] ) ) {
        $head = $at;
        break;
      }

    if ( $head === NULL ) {
      foreach ( $lines as $at => $line )
        if ( str_contains ( $line, '|' ) )
          padFormatFail ( $at + 2, 'a pipe table has a row of dashes under its header - | --- | --- |' );
      padFormatFail ( 1, 'there is no pipe table - a header row, | name | age |, then a row of dashes, | --- | --- |' );
    }

    $names  = array_map ( 'trim', padMarkdownCells ( $lines [$head] ) );
    $dashes = padMarkdownCells ( $lines [ $head + 1 ] );

    if ( count ( $dashes ) != count ( $names ) )
      padFormatFail ( $head + 2, 'the row of dashes has ' . count ( $dashes ) . ' cells under a header of ' . count ( $names ) );

    $rows = [];

    for ( $at = $head + 2; $at < $count; $at++ ) {

      if ( trim ( $lines [$at] ) === '' or ! str_contains ( $lines [$at], '|' ) )
        break;

      $cells = padMarkdownCells ( $lines [$at] );
      $row   = [];

      foreach ( $names as $index => $name )
        $row [$name] = $cells [$index] ?? '';

      $rows [] = $row;

    }

    return $rows;

  }

  // A row of dashes - with a pipe in it: --- alone is a rule, or a heading's underline.

  function padMarkdownDashes ( $line ) {

    return preg_match ( '/^[ \t]*\|?[ \t]*:?-+:?[ \t]*(\|[ \t]*:?-+:?[ \t]*)*\|?[ \t]*\r?$/D', $line ) and str_contains ( $line, '|' );

  }

  // The cells of a row: split at the pipes that are no \|, the pipes at the ends left off,
  // each cell trimmed, \| a pipe again and <br> a newline.

  function padMarkdownCells ( $line ) {

    $line = trim ( $line );

    if ( str_starts_with ( $line, '|' ) )
      $line = substr ( $line, 1 );

    if ( str_ends_with ( $line, '|' ) and ! str_ends_with ( $line, '\\|' ) )
      $line = substr ( $line, 0, -1 );

    $cells = preg_split ( '/(?<!\\\\)\|/', $line );

    return array_map ( fn ( $cell ) => preg_replace ( '/<br\s*\/?>/i', "\n", str_replace ( '\\|', '|', trim ( $cell ) ) ), $cells );

  }

  function padMarkdownWrite ( $rows ) {

    $columns = padFormatColumns ( $rows );

    if ( ! $columns )
      return '';

    $cells   = [];
    $numbers = array_fill_keys ( $columns, TRUE );

    foreach ( $rows as $row ) {
      $line = [];
      foreach ( $columns as $column ) {
        $value = $row [$column] ?? '';
        if ( $value !== '' and $value !== NULL and ! is_int ( $value ) and ! is_float ( $value ) and ! is_numeric ( $value ) )
          $numbers [$column] = FALSE;
        $line [] = padMarkdownCell ( padFormatCell ( $value ) );
      }
      $cells [] = $line;
    }

    $header = array_map ( 'padMarkdownCell', $columns );
    $width  = [];

    foreach ( $columns as $index => $column ) {
      $width [$index] = max ( 3, mb_strlen ( $header [$index] ) );
      foreach ( $cells as $line )
        $width [$index] = max ( $width [$index], mb_strlen ( $line [$index] ) );
    }

    $right = array_values ( $numbers );
    $pad   = fn ( $text, $index ) => $right [$index]
                                     ? str_repeat ( ' ', $width [$index] - mb_strlen ( $text ) ) . $text
                                     : $text . str_repeat ( ' ', $width [$index] - mb_strlen ( $text ) );

    $out   = [];
    $out [] = '| ' . implode ( ' | ', array_map ( $pad, $header, array_keys ( $header ) ) ) . ' |';
    $out [] = '| ' . implode ( ' | ', array_map ( fn ( $index ) => $right [$index] ? str_repeat ( '-', $width [$index] - 1 ) . ':' : str_repeat ( '-', $width [$index] ), array_keys ( $columns ) ) ) . ' |';

    foreach ( $cells as $line )
      $out [] = '| ' . implode ( ' | ', array_map ( $pad, $line, array_keys ( $line ) ) ) . ' |';

    return implode ( "\n", $out ) . "\n";

  }

  function padMarkdownCell ( $text ) {

    return str_replace ( [ '|', "\r\n", "\n", "\r" ], [ '\\|', '<br>', '<br>', '' ], trim ( (string) $text ) );

  }

?>
