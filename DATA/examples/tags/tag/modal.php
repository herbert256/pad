<?php

  $tagAbout   = 'A dialog over the page with the button that opens it - works with and without JavaScript.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{modal 'name'} ... {/modal}
{modal 'name', title='Heading', button='Button text'} ... {/modal}
PAD;

  $tagParms   = [
    'name' => 'Letters, digits, <code>_</code> and <code>-</code>, at most 64. The dialog\'s id is <code>pad-modal-&lt;name&gt;</code>.' ];

  $tagOptions = [
    'title'  => 'The heading of the dialog, which also names it for a screen reader. The button\'s text when not given.',
    'button' => 'The text of the button that opens the dialog, <code>Open</code> when not given.' ];

  $tagSee     = [ 'tabs', 'accordion', 'carousel', 'copy' ];

?>
