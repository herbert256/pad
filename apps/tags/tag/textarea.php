<?php

  $tagAbout   = 'A text area with its label that refills from what its form sent and shows the error validation found for it.';
  $tagGroup   = 'forms';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{textarea 'message'}
{textarea 'message', label='Message', value='', id='msg', rows=6, required}
{textarea 'message', label='Message', rows=6, rules='required|max:2000'}
PAD;

  $tagParms   = [
    'name' => 'The field name - required.' ];

  $tagOptions = [
    'label' => 'A <code>&lt;label for&gt;</code> before the text area, and the word its error message uses.',
    'value' => 'The text before anything was sent, escaped.',
    'id'    => 'The id, by default the name.',
    'rules' => 'The field\'s rules - <code>required|max:2000</code> - read from the page\'s own template and checked before its PHP runs. Only in a named <code>{form}</code> of the page itself.' ];

  $tagSee     = [ 'input', 'form', 'attrs' ];

?>
