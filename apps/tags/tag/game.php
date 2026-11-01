<?php

  // The games are the files of pad/subs/games/types/, as the engine reads them (padGameTypes).

  $tagAbout   = 'A little game played in the browser - ' . count ( padGameTypes () ) . ' of them, from sudoku and a crossword to snake, reversi and a quiz.';
  $tagGroup   = 'games';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{game 'name'/}
{game 'tictactoe', opens='computer'/}
{game:memory pairs=6/}
{sudoku 'puzzle'/}
{sudoku givens=26, seed=7/}
{sudoku 'puzzle', paper/}
{crossword} WORD: clue ... {/crossword}
{hangman} word ... {/hangman}
{quiz} question | right | wrong ... {/quiz}
PAD;

  $tagParms   = [
    'name' => 'The game, a file of <code>pad/subs/games/types/</code>: '
            . implode ( ', ', array_map ( fn ( $game ) => "<code>$game</code>", padGameTypes () ) )
            . '. The game: type names it as well - <code>{game:memory/}</code>, or bare, <code>{memory/}</code>, when nothing else has the name.' ];

  $tagOptions = [
    'seed'     => 'A whole number: the shuffles, the boards and the puzzles are the same on every load. Without it the browser picks.',
    'title'    => 'The name the game is shown under, else its own.',
    'data'     => 'The words, the symbols or the questions of a game that takes them, from a <code>{data}</code> store, the page\'s array or a <code>_data</code> file - else they stand between the tags, one a line.',
    'opens'    => 'tictactoe, gomoku, reversi, nim, dotsandboxes, mancala: <code>you</code> (the default) or <code>computer</code>.',
    'two'      => 'Bare option, tictactoe, gomoku, reversi, dotsandboxes, mancala: two players at one screen instead of the computer.',
    'size'     => 'gomoku (15), fifteen (4), snake (18), wordsearch: the size of the board.',
    'height'   => 'maze: the rows of cells (12), <code>width</code> the columns (18); dotsandboxes: the rows of boxes (4), <code>width</code> the columns (4).',
    'level'    => 'mancala: how far the computer looks ahead - <code>easy</code>, <code>normal</code> (the default) or <code>hard</code>.',
    'seeds'    => 'mancala: the seeds each house starts with (4).',
    'pairs'    => 'memory: how many pairs (8).',
    'tries'    => 'hangman: wrong letters allowed (6); bullscows: guesses allowed (10).',
    'digits'   => 'bullscows: how long the secret number is (4).',
    'disks'    => 'hanoi: how many discs (4).',
    'heaps'    => 'nim: the heaps, <code>\'3, 4, 5\'</code>; <code>misere</code> - who takes the last match loses.',
    'rounds'   => 'scramble: words a game (10).',
    'speed'    => 'snake: steps a second it starts with (6); <code>wrap</code> lets it run off one side and on at the other.',
    'givens'   => 'sudoku without a puzzle: how many squares the new puzzle shows (30).',
    'paper'    => 'Bare option, sudoku and crossword: the puzzle drawn to print, no script.',
    'solve'    => 'Bare option, sudoku: the puzzle drawn with its solution. <code>solution</code> does it for a crossword.',
    'order'    => 'Bare option, quiz: the questions in the order written.' ];

  $tagSee     = [ 'notation:chess', 'chart', 'data' ];

?>
