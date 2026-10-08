<?php

  $tagAbout   = 'A condition a test run holds the page to; silent everywhere else.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{assert condition}
{assert condition, 'message'}
PAD;

  $tagParms   = [
    'condition' => 'An expression, as in <a href="?tag/if">{if}</a>: <code>$total eq 42</code>. Read raw and evaluated only when asserts are on.',
    'message'   => 'Optional. An expression - usually a quoted text - added to the report: <code>assert failed: $total eq 42 - the cart total</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'if', 'error' ];

?>
