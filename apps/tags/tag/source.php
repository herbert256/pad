<?php

  $tagAbout   = 'Shows the files behind a page as tabs, each coloured as highlight colours it - the code shown is the code that runs.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{source}
{source 'page', files='_lib/orders.php, www:orders.js'}
{source only='_tags/card.pad, www:card.js'}
PAD;

  $tagParms   = [
    'page' => 'The page whose <code>.pad</code> (or <code>.html</code>) and <code>.php</code> come first - default the page being rendered.' ];

  $tagOptions = [
    'files' => 'Files after the page\'s, a text with commas: a file of the application, or <code>www:</code> one in its <code>www/</code> directory.',
    'only'  => 'Those files alone, without the page\'s - the same kind of list as <code>files=</code>.' ];

  $tagSee     = [ 'highlight', 'tabs', 'page' ];

?>
