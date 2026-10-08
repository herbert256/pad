<?php

  $tagAbout   = 'Abandons the page being built and renders another page in its place, in the same request.';
  $tagGroup   = 'pages';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{restart 'name'}
{restart 'name', $message = 'Saved'}
PAD;

  $tagParms   = [
    'page' => 'The page to render instead, named as in a URL.' ];

  $tagOptions = [];

  $tagSee     = [ 'redirect', 'page', 'exit' ];

?>
