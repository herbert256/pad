<?php

  $tagAbout   = 'Throws a real PHP exception from the template, to exercise the error handling.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{exception 'message'}
PAD;

  $tagParms   = [
    'message' => 'The message of the <code>Exception</code> that is thrown.' ];

  $tagOptions = [];

  $tagSee     = [ 'error', 'exit', 'dump' ];

?>
