<?php

  // life - Conway's Game of Life: a grid of cells, each alive or dead, where a dead cell with
  // three live neighbours is born and a live one with two or three lives on. Draw cells with
  // the pointer or the arrow keys and Enter, then play, step, clear or scatter new ones, or
  // drop in a pattern - a glider, a spaceship, the glider gun, a pulsar. width= and height= the
  // cells across and down (64 by 40 unless given, 10 to 160); speed= the generations a second
  // (10, 1 to 60); pattern= what it starts with; wrap joins the edges; rule= another rule of
  // the family, B36/S23 for HighLife. A pattern of your own stands between the tags, a line
  // per row, O or # for a live cell and . for a dead one.
  //
  //   {life pattern='gun', wrap/}
  //   {life}
  //     .O.
  //     ..O
  //     OOO
  //   {/life}

  // The patterns to pick from, a row a line - O alive, . dead; random scatters cells, empty
  // leaves the grid to be drawn on.

  $padLifePatterns = [
    'glider'   => [ '.O.', '..O', 'OOO' ],
    'spaceship'=> [ '.O..O', 'O....', 'O...O', 'OOOO.' ],
    'pulsar'   => [ '..OOO...OOO..', '.............', 'O....O.O....O', 'O....O.O....O', 'O....O.O....O',
                    '..OOO...OOO..', '.............', '..OOO...OOO..', 'O....O.O....O', 'O....O.O....O',
                    'O....O.O....O', '.............', '..OOO...OOO..' ],
    'gun'      => [ '........................O...........', '......................O.O...........',
                    '............OO......OO............OO', '...........O...O....OO............OO',
                    'OO........O.....O...OO..............', 'OO........O...O.OO....O.O...........',
                    '..........O.....O.......O...........', '...........O...O....................',
                    '............OO......................' ],
    'pentadecathlon' => [ '..O....O..', 'OO.OOOO.OO', '..O....O..' ],
    'rpentomino' => [ '.OO', 'OO.', '.O.' ],
    'acorn'    => [ '.O.....', '...O...', 'OO..OOO' ],
    'diehard'  => [ '......O.', 'OO......', '.O...OOO' ],
  ];

  $padLifeWidth  = padToyInt ( 'width', 64, 10, 160 );
  $padLifeHeight = padToyInt ( 'height', 40, 10, 160 );

  // A pattern written between the tags: rows of O or # and ., as long as each other or not -
  // a short row is dead at its end. A line that starts with # is a row when it is one, #.#,
  // and a comment when it is not, # a glider.

  $padLifeOwn = [];

  foreach ( padToyLines ( '/^[O#*.]+$/iD' ) as $padLifeRow ) {
    if ( ! preg_match ( '/^[O#*.]+$/iD', $padLifeRow ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the " . padToyWhat () . " pattern has a row '" . padMakeSafe ( $padLifeRow, 30 ) . "' - O or # for a live cell, . for a dead one" );
      $padLifeOwn = [];
      break;
    }
    $padLifeOwn [] = strtr ( strtoupper ( $padLifeRow ), '#*', 'OO' );
  }

  if ( $padLifeOwn and ( count ( $padLifeOwn ) > $padLifeHeight or max ( array_map ( 'strlen', $padLifeOwn ) ) > $padLifeWidth ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " pattern is larger than the grid of $padLifeWidth by $padLifeHeight" );
    $padLifeOwn = [];
  }

  if ( $padLifeOwn )
    $padLifePatterns = [ 'yours' => $padLifeOwn ] + $padLifePatterns;

  $padLifeRule = padParmText ( padTagParm ( 'rule', 'B3/S23' ) );

  if ( ! preg_match ( '/^B([0-8]*)\/S([0-8]*)$/iD', trim ( $padLifeRule ), $padLifeMatch ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no rule '" . padMakeSafe ( $padLifeRule, 20 ) . "' - B and the neighbours a cell is born with, / S and the ones it lives on with: B3/S23" );
    $padLifeMatch = [ '', '3', '23' ];
  }

  return padToy ( 'life', 'Game of Life', [
    'width'    => $padLifeWidth,
    'height'   => $padLifeHeight,
    'speed'    => padToyInt ( 'speed', 10, 1, 60 ),
    'wrap'     => padToyFlag ( 'wrap' ),
    'born'     => array_values ( array_unique ( array_map ( 'intval', preg_split ( '//', $padLifeMatch [1], -1, PREG_SPLIT_NO_EMPTY ) ) ) ),
    'survive'  => array_values ( array_unique ( array_map ( 'intval', preg_split ( '//', $padLifeMatch [2], -1, PREG_SPLIT_NO_EMPTY ) ) ) ),
    'rule'     => 'B' . $padLifeMatch [1] . '/S' . $padLifeMatch [2],
    'pattern'  => padToyChoice ( 'pattern', $padLifeOwn ? 'yours' : 'random', array_merge ( array_keys ( $padLifePatterns ), [ 'random', 'empty' ] ) ),
    'patterns' => $padLifePatterns
  ] );

?>
