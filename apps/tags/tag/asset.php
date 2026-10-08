<?php

  $tagAbout   = 'The address of a file of www/<app>/ with a version of its contents, safe behind a far-future cache header.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{asset 'file'}
{asset 'file', tag}
PAD;

  $tagParms   = [
    'file' => 'A file relative to <code>www/&lt;app&gt;/</code> - <code>\'charts.css\'</code>, <code>\'js/app.js\'</code>. It never leaves that directory.' ];

  $tagOptions = [
    'tag' => 'Bare option: write the element instead of the address - <code>&lt;link rel="stylesheet"&gt;</code> for <code>.css</code>, <code>&lt;script defer&gt;</code> for <code>.js</code>, <code>&lt;script type="module"&gt;</code> for <code>.mjs</code>.' ];

  $tagSee     = [ 'vite', 'nonce', 'flush' ];

?>
