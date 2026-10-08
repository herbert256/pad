<?php

  // Sudoku - the {sudoku} tag. A puzzle of 81 cells, row by row, drawn as a table: the
  // givens bold, the empty cells open, and with solve the solution filled in by a
  // backtracking solver in another colour.
  //
  //   {sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8.79'}
  //   {sudoku $puzzle, solve, title='Saturday puzzle'}
  //
  // padSudokuCells   the 81 cells of a text - 0 for an empty one - or the reason it is no
  //                  puzzle as a string
  // padSudokuSolve   the cells solved, or NULL when there is no solution
  // padSudoku        the table
  //
  // The text has a digit 1-9 for a given and . 0 _ or - for an empty cell; whitespace and
  // the | and + of a drawn grid are passed over, so a puzzle can be written in nine lines.
  // The solver fills the cell with the fewest candidates first - every puzzle with a
  // solution is solved in a few milliseconds, the hardest in well under a second.

  function padSudokuCells ( $text ) {

    $text  = preg_replace ( '/[\s|+]+/', '', (string) $text );
    $cells = [];

    foreach ( str_split ( $text ) as $char )
      if ( $char >= '1' and $char <= '9' )
        $cells [] = (int) $char;
      elseif ( strpos ( '.0_-', $char ) !== FALSE )
        $cells [] = 0;
      else
        return "it has a '$char' - a given is a digit 1-9, an empty cell a . or 0";

    if ( count ( $cells ) != 81 )
      return 'it has ' . count ( $cells ) . ' cells, not 81';

    // Two givens of the same digit in a row, a column or a box.

    for ( $i = 0; $i < 81; $i++ )
      for ( $j = $i + 1; $j < 81 and $cells [$i]; $j++ )
        if ( $cells [$i] == $cells [$j] and padSudokuSees ( $i, $j ) )
          return 'the ' . $cells [$i] . ' in row ' . ( intdiv ( $i, 9 ) + 1 ) . ', column ' . ( $i % 9 + 1 )
               . ' is there again in row ' . ( intdiv ( $j, 9 ) + 1 ) . ', column ' . ( $j % 9 + 1 );

    return $cells;

  }

  // Whether two cells share a row, a column or a box.

  function padSudokuSees ( $i, $j ) {

    return intdiv ( $i, 9 ) == intdiv ( $j, 9 ) or $i % 9 == $j % 9
        or ( intdiv ( $i, 27 ) == intdiv ( $j, 27 ) and intdiv ( $i % 9, 3 ) == intdiv ( $j % 9, 3 ) );

  }

  // The solver: a bit per digit for what each row, column and box holds, and depth first
  // from the empty cell with the fewest candidates - none left is a dead end, one left is
  // filled without a guess.

  function padSudokuSolve ( $cells ) {

    $rows = $cols = $boxes = array_fill ( 0, 9, 0 );

    foreach ( $cells as $i => $digit )
      if ( $digit ) {
        $bit = 1 << $digit;
        $rows  [ intdiv ( $i, 9 ) ] |= $bit;
        $cols  [ $i % 9 ]           |= $bit;
        $boxes [ padSudokuBox ( $i ) ] |= $bit;
      }

    return padSudokuSearch ( $cells, $rows, $cols, $boxes ) ? $cells : NULL;

  }

  function padSudokuBox ( $i ) {

    return intdiv ( $i, 27 ) * 3 + intdiv ( $i % 9, 3 );

  }

  function padSudokuSearch ( &$cells, &$rows, &$cols, &$boxes ) {

    $best = -1;
    $free = 0;
    $low  = 10;

    foreach ( $cells as $i => $digit )
      if ( ! $digit ) {
        $open  = ~ ( $rows [ intdiv ( $i, 9 ) ] | $cols [ $i % 9 ] | $boxes [ padSudokuBox ( $i ) ] ) & 0x3FE;
        $count = substr_count ( decbin ( $open ), '1' );
        if ( $count < $low ) {
          list ( $best, $free, $low ) = [ $i, $open, $count ];
          if ( $count <= 1 )
            break;
        }
      }

    if ( $best < 0 )
      return TRUE;

    $r = intdiv ( $best, 9 );
    $c = $best % 9;
    $b = padSudokuBox ( $best );

    for ( $digit = 1; $digit <= 9; $digit++ )
      if ( $free & ( 1 << $digit ) ) {
        $bit = 1 << $digit;
        $cells [$best] = $digit;
        $rows [$r] |= $bit; $cols [$c] |= $bit; $boxes [$b] |= $bit;
        if ( padSudokuSearch ( $cells, $rows, $cols, $boxes ) )
          return TRUE;
        $rows [$r] &= ~$bit; $cols [$c] &= ~$bit; $boxes [$b] &= ~$bit;
      }

    $cells [$best] = 0;

    return FALSE;

  }

  // The table: a row per row of the puzzle, the boxes set apart by heavier lines. A given
  // is a <b>, a solved digit a cell of its own colour, an empty cell says so to
  // a screen reader. The caption is the title, else the table is named by aria-label.

  function padSudoku ( $cells, $solved, $title ) {

    $h      = fn ( $t ) => htmlspecialchars ( (string) $t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    $givens = count ( array_filter ( $cells ) );
    $name   = $solved ? "Sudoku with its solution, $givens givens" : "Sudoku, $givens givens";

    $html = padSudokuStyle () . '<table class="pad-sudoku' . ( $solved ? ' is-solved' : '' ) . '"'
          . ( $title === '' ? ' aria-label="' . $h ( $name ) . '"' : '' ) . '>'
          . ( $title !== '' ? '<caption>' . $h ( $title ) . '</caption>' : '' )
          . '<tbody>';

    for ( $r = 0; $r < 9; $r++ ) {

      $html .= '<tr>';

      for ( $c = 0; $c < 9; $c++ ) {
        $i = $r * 9 + $c;
        if ( $cells [$i] )
          $html .= '<td class="psd-given"><b>' . $cells [$i] . '</b></td>';
        elseif ( $solved )
          $html .= '<td class="psd-solved">' . $solved [$i] . '</td>';
        else
          $html .= '<td class="psd-open"><span class="psd-hide">empty</span></td>';
      }

      $html .= '</tr>';

    }

    return "$html</tbody></table>";

  }

  // The look, once per page: the grid lines, the given and the solved digits as custom
  // properties with light-dark() defaults.

  function padSudokuStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'line'   => [ '#b9b8b2', '#4a4a46' ], 'box'    => [ '#2b2a28', '#c3c2b7' ],
               'given'  => [ '#1a1a19', '#f4f3ee' ], 'solved' => [ '#2a78d6', '#5598e7' ],
               'cell'   => [ '#ffffff', '#1f1f1d' ], 'shade'  => [ '#f4f3ef', '#262624' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-sudoku-$role:$day;";
      $both  .= "--pad-sudoku-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-sudoku){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-sudoku){{$both}}}"
         . ':where(.pad-sudoku){border-collapse:collapse;border:3px solid var(--pad-sudoku-box);background:var(--pad-sudoku-cell);font:22px/1 system-ui,sans-serif;margin:0 auto}'
         . ':where(.pad-sudoku caption){padding:6px;font-weight:600;font-size:16px}'
         . ':where(.pad-sudoku td){width:2em;height:2em;padding:0;text-align:center;vertical-align:middle;border:1px solid var(--pad-sudoku-line)}'
         . ':where(.pad-sudoku td:nth-child(3n)){border-right:3px solid var(--pad-sudoku-box)}'
         . ':where(.pad-sudoku tr:nth-child(3n) td){border-bottom:3px solid var(--pad-sudoku-box)}'
         . ':where(.pad-sudoku .psd-given){background:var(--pad-sudoku-shade);color:var(--pad-sudoku-given)}'
         . ':where(.pad-sudoku .psd-given b){font-weight:700}'
         . ':where(.pad-sudoku .psd-solved){color:var(--pad-sudoku-solved);font-weight:400}'
         . '.pad-sudoku .psd-hide{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap}'
         . '</style>';

  }

?>
