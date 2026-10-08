<?php

  $tagAbout   = 'Evaluates an expression and prints the result - the general output tag, and the one to put in front of a pipe.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{echo expression}
{echo expression | pipe | pipe}
PAD;

  $tagParms   = [
    'expression' => 'A PAD expression: fields, literals, arithmetic, comparisons, a ternary - and pipes after it. The whole text up to the closing brace is the expression.' ];

  $tagOptions = [];

  $tagSee     = [ 'set', 'field', 'ignore', 'trans' ];

?>
