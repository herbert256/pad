<?php

  $tagAbout   = 'Fetches another page of the application over HTTP, as a second request, and inserts its output.';
  $tagGroup   = 'pages';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{get 'name'}
{get 'name', $colour = 'red'}
PAD;

  $tagParms   = [
    'page' => 'The page of this application to fetch, as in a URL - <code>\'orders/list\'</code>; a clean URL route counts.' ];

  $tagOptions = [];

  $tagSee     = [ 'page', 'ajax', 'curl', 'content' ];

?>
