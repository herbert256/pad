<?php

  $tagAbout   = 'Writes an empty div and a script that has the browser fetch another page into it after the page has loaded.';
  $tagGroup   = 'pages';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{ajax 'name'}
{ajax 'name', fragment='fragmentName'}
{ajax 'name', app='other'}
{ajax 'name', $colour = 'red'}
PAD;

  $tagParms   = [
    'page' => 'The page to fetch, named as in a URL - <code>\'orders/list\'</code>.' ];

  $tagOptions = [
    'fragment' => 'Ask the page for one of its response fragments alone - <code>&amp;padFragment=name</code> - instead of the whole page.',
    'app'      => 'A page of another application; under the strict check it must exist there.' ];

  $tagSee     = [ 'page', 'get', 'live', 'fragment' ];

?>
