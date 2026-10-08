<?php

  $tagAbout   = 'A text in a code block with a button that copies it to the clipboard.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{copy 'text'}
{copy $value, label='Copy command'}
{copy label='Copy'} ... {/copy}
PAD;

  $tagParms   = [
    'text' => 'The text to show and copy - a value, escaped. Not used by the pair form, whose content is the text.' ];

  $tagOptions = [
    'label' => 'The text of the button, <code>Copy</code> when not given.' ];

  $tagSee     = [ 'highlight', 'source', 'modal' ];

?>
