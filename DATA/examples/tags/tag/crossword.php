<?php

  $tagAbout   = 'A crossword made from a list of words: a numbered SVG grid with the Across and Down clues.';
  $tagGroup   = 'games';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{crossword} WORD: clue ... {/crossword}
{crossword solution, title='Name'} WORD: clue ... {/crossword}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'solution' => 'Bare option: write the letters in the grid and the answers behind the clues.',
    'title'    => 'The grid\'s accessible name, <code>Crossword</code> when not given.' ];

  $tagSee     = [ 'sudoku', 'chess' ];

?>
