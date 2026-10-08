<?php

  $tagAbout   = 'Switches how the running request answers - a web page, the console, a file, a download, JSON or CSV.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{output 'type'}
PAD;

  $tagParms   = [
    'type' => 'One of <code>web</code>, <code>console</code>, <code>file</code>, <code>download</code>, <code>json</code> or <code>csv</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'flush', 'tidy', 'page' ];

?>
