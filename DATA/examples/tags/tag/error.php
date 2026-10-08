<?php

  $tagAbout   = 'Raises a PAD error from the template, handled as the configured error action says.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{error 'message'}
PAD;

  $tagParms   = [
    'message' => 'The text of the error. The report shows it as <code>PAD: message</code>, with the template position of the tag.' ];

  $tagOptions = [];

  $tagSee     = [ 'exception', 'assert', 'exit', 'dump' ];

?>
