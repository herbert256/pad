<?php

  $tagAbout   = 'Links the scripts and stylesheets of a Vite build by the name of their source - or the dev server while it runs.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{vite 'src/main.js'}
{vite 'src/main.jsx', 'src/admin.js', react}
PAD;

  $tagParms   = [
    'entry' => 'An entry of the build, as <code>build.rollupOptions.input</code> names it. Several entries may follow each other.' ];

  $tagOptions = [
    'react' => 'Bare option: with the dev server, write the preamble the React plugin needs for fast refresh first.' ];

  $tagSee     = [ 'asset', 'reactData', 'nonce' ];

?>
