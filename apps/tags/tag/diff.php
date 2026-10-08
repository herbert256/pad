<?php

  $tagAbout   = 'Shows what changed between two texts - word by word in the flow of the text, or line by line in one column or side by side.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{diff $old, $new}
{diff $old, $new, lines, from='Monday', to='Friday'}
{diff $old, $new, side, from='before', to='after'}
PAD;

  $tagParms   = [
    'old' => 'The old text.',
    'new' => 'The new text.' ];

  $tagOptions = [
    'lines' => 'Bare option: compare line by line - a table with the old and the new line number, a sign and the line.',
    'side'  => 'Bare option: the lines side by side, a changed line beside its counterpart, line numbers on both sides.',
    'from'  => 'The name of the old version - a column head side by side, a caption in one column.',
    'to'    => 'The name of the new version.' ];

  $tagSee     = [ 'excerpt', 'highlight', 'markdown' ];

?>
