<?php

  // Sudoku - the {sudoku} tag. A puzzle of 81 cells, row by row, drawn as a table: the
  // givens bold, the empty cells open, and with solve the solution filled in by a
  // backtracking solver in another colour.
  //
  //   {sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8..79'}
  //   {sudoku $puzzle, solve, title='Saturday puzzle'}
  //
  // padSudokuCells   the 81 cells of a text - 0 for an empty one - or the reason it is no
  //                  puzzle as a string
  // padSudokuSolve   the cells solved - the first of many, which $many says - NULL when
  //                  there is no solution, FALSE when the search gave up before it could tell
  // padSudoku        the table
  //
  // The text has a digit 1-9 for a given and . 0 _ or - for an empty cell; whitespace and
  // the | and + of a drawn grid are passed over, so a puzzle can be written in nine lines.
  // The solver places first what has the fewest ways to go: the cell with the fewest
  // candidates, or the digit with the fewest places left in a row, column or box - a
  // digit with one place is filled without a guess, one with none ends the branch. Every
  // published puzzle is solved in a few milliseconds; the search gives up after 50,000
  // steps, about a second, where the cell alone took a minute over a sparse puzzle with no
  // solution and the request ran into its time limit.

  function padSudokuCells ( $text ) {

    // A line of a drawn grid between its bands - ------+-------+------ - is no row: its
    // dashes were read as 38 empty cells. A line of dashes alone is one when it holds a +
    // or a = - no cell is written with either - or more dashes than the nine a row of empty
    // cells has, counted without the spaces and bars between them, which made - - - | - - -
    // | - - -, a row with no given, a band line. The line ends of a posted textarea are
    // \r\n, and the \r kept a band line from ending where the test looked for its end.

    $text  = str_replace ( "\r", '', (string) $text );
    $text  = preg_replace_callback ( '/^[-+=| \t]+$/m',
               fn ( $line ) => ( strpbrk ( $line [0], '+=' ) !== FALSE or substr_count ( $line [0], '-' ) > 9 ) ? '' : $line [0],
               $text );

    // A line of nine dashes is a row of empty cells, or the band line of a compact grid as
    // wide as its rows - ---|---|--- under 53.|.7.|... - and the count of cells tells which:
    // band lines when the grid has 81 without such lines and more with them, where they were
    // read as rows, 99 cells.

    $count = fn ( $text ) => strlen ( preg_replace ( '/[\s|+]+/', '', $text ) );
    $bare  = preg_replace ( '/^[-| \t]+$/m', '', $text );

    if ( $count ( $text ) > 81 and $count ( $bare ) == 81 )
      $text = $bare;

    $text  = preg_replace ( '/[\s|+]+/', '', $text );
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
  // from what has the fewest ways to go.

  // A puzzle has one solution: the search goes on past the first for a second, and $many
  // says it found one - a grid of nothing, or one given too few, was shown as "its
  // solution", one of thousands. Out of steps after the first, the first stands.

  function padSudokuSolve ( $cells, &$many = NULL ) {

    $rows   = $cols = $boxes = array_fill ( 0, 9, 0 );
    $budget = 50000;

    foreach ( $cells as $i => $digit )
      if ( $digit ) {
        $bit = 1 << $digit;
        $rows  [ intdiv ( $i, 9 ) ] |= $bit;
        $cols  [ $i % 9 ]           |= $bit;
        $boxes [ padSudokuBox ( $i ) ] |= $bit;
      }

    $solutions = [];
    $found     = padSudokuSearch ( $cells, $rows, $cols, $boxes, $budget, $solutions );
    $many      = ( count ( $solutions ) > 1 );

    if ( $solutions )
      return $solutions [0];

    return ( $found === NULL ) ? FALSE : NULL;

  }

  function padSudokuBox ( $i ) {

    return intdiv ( $i, 27 ) * 3 + intdiv ( $i % 9, 3 );

  }

  // The 27 units - the rows, the columns and the boxes - as lists of their cells.

  function padSudokuUnits () {

    static $units = NULL;

    if ( $units === NULL ) {

      $units = [];

      for ( $u = 0; $u < 9; $u++ ) {

        $row = $col = $box = [];

        for ( $k = 0; $k < 9; $k++ ) {
          $row [] = $u * 9 + $k;
          $col [] = $k * 9 + $u;
          $box [] = ( intdiv ( $u, 3 ) * 3 + intdiv ( $k, 3 ) ) * 9 + ( $u % 3 ) * 3 + $k % 3;
        }

        array_push ( $units, $row, $col, $box );

      }

    }

    return $units;

  }

  // TRUE solved - the cells filled in -, FALSE no solution, NULL the budget spent.
  //
  // The row, column and box of each cell and the number of candidates of each mask come
  // from tables made once, and a unit's empty cells and the digits it holds are gathered
  // once per unit, where every step called padSudokuBox for each of the 81 cells, counted a
  // mask's bits as a string of ones and zeros, and went over all nine cells of a unit for
  // each of its nine digits, building the list of its places: the same steps in the same
  // order, in half the time.

  function padSudokuSearch ( &$cells, &$rows, &$cols, &$boxes, &$budget, &$solutions ) {

    static $row = NULL, $col, $box, $bits;

    if ( $row === NULL ) {
      for ( $i = 0; $i < 81; $i++ ) {
        $row [$i] = intdiv ( $i, 9 );
        $col [$i] = $i % 9;
        $box [$i] = padSudokuBox ( $i );
      }
      for ( $mask = 0; $mask < 1024; $mask++ )
        $bits [$mask] = substr_count ( decbin ( $mask ), '1' );
    }

    if ( --$budget < 0 )
      return NULL;

    $open = [];
    $best = -1;
    $low  = 10;

    foreach ( $cells as $i => $digit )
      if ( ! $digit ) {
        $open [$i] = ~ ( $rows [ $row [$i] ] | $cols [ $col [$i] ] | $boxes [ $box [$i] ] ) & 0x3FE;
        $count     = $bits [ $open [$i] ];
        if ( $count < $low ) {
          $best = $i;
          $low  = $count;
        }
      }

    if ( $best < 0 ) {
      $solutions [] = $cells;
      return count ( $solutions ) > 1;
    }

    if ( $low == 0 )
      return FALSE;

    // A digit with fewer places in a unit than the cell has candidates goes first; one with
    // no place left is a dead end.

    $choices = NULL;

    if ( $low > 1 )
      foreach ( padSudokuUnits () as $unit ) {

        $empty = [];
        $held  = 0;

        foreach ( $unit as $i )
          if ( $cells [$i] )
            $held |= 1 << $cells [$i];
          else
            $empty [] = $i;

        for ( $digit = 1; $digit <= 9; $digit++ ) {

          $bit = 1 << $digit;

          if ( $held & $bit )
            continue;

          $count = 0;

          foreach ( $empty as $i )
            if ( $open [$i] & $bit )
              $count++;

          if ( ! $count )
            return FALSE;

          if ( $count < $low ) {
            $low     = $count;
            $choices = [];
            foreach ( $empty as $i )
              if ( $open [$i] & $bit )
                $choices [] = [ $i, $digit ];
            if ( $low == 1 )
              break 2;
          }

        }

      }

    if ( $choices === NULL ) {
      $choices = [];
      for ( $digit = 1; $digit <= 9; $digit++ )
        if ( $open [$best] & ( 1 << $digit ) )
          $choices [] = [ $best, $digit ];
    }

    foreach ( $choices as [ $i, $digit ] ) {

      $bit = 1 << $digit;
      $r   = $row [$i];
      $c   = $col [$i];
      $b   = $box [$i];

      $cells [$i] = $digit;
      $rows [$r] |= $bit; $cols [$c] |= $bit; $boxes [$b] |= $bit;

      $found = padSudokuSearch ( $cells, $rows, $cols, $boxes, $budget, $solutions );

      if ( $found !== FALSE )
        return $found;

      $rows [$r] &= ~$bit; $cols [$c] &= ~$bit; $boxes [$b] &= ~$bit;
      $cells [$i] = 0;

    }

    return FALSE;

  }

  // The table: a row per row of the puzzle, the boxes set apart by heavier lines. A given
  // is a <b>, a solved digit a cell of its own colour, an empty cell says so to
  // a screen reader. The caption is the title, else the table is named by aria-label.

  function padSudoku ( $cells, $solved, $title ) {

    $h      = fn ( $t ) => htmlspecialchars ( padUnprotectQuotes ( (string) $t ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
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

    if ( ! padWidgetOnce ( 'style:sudoku' ) )
      return '';

    $roles = [ 'line'   => [ '#b9b8b2', '#4a4a46' ], 'box'    => [ '#2b2a28', '#c3c2b7' ],
               'given'  => [ '#1a1a19', '#f4f3ee' ], 'solved' => [ '#2a78d6', '#5598e7' ],
               'cell'   => [ '#ffffff', '#1f1f1d' ], 'shade'  => [ '#f4f3ef', '#262624' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-sudoku-$role:$day;";
      $both  .= "--pad-sudoku-$role:light-dark($day,$night);";
    }

    return '<style' . padStyleNonce () . '>'
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
