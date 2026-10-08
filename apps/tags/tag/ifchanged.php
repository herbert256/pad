<?php

  $tagAbout   = 'Renders its content when a value differs from the previous row of the loop around it - a heading per group.';
  $tagGroup   = 'conditions';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{ifchanged value} ... {/ifchanged}
{ifchanged value, value} ... {/ifchanged}
{ifchanged value} ... @else@ ... {/ifchanged}
PAD;

  $tagParms   = [
    'value' => 'One or more values, usually fields of the current row. The content renders when any of them differs from what it was on the previous row.' ];

  $tagOptions = [];

  $tagSee     = [ 'if', 'case', 'switch' ];

?>
