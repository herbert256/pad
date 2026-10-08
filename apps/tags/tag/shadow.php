<?php

  $tagAbout   = 'The shadow root of a custom element, rendered on the server as declarative shadow DOM.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{shadow} ... {/shadow}
{shadow css='file.css, www:other.css', mode='closed', focus} ... {/shadow}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'css'   => 'Stylesheets inlined as one <code>&lt;style&gt;</code> at the start of the root, separated by commas: a <code>.css</code> file of the application, or <code>www:</code> one of its <code>www/</code> directory.',
    'mode'  => '<code>open</code> (the default) or <code>closed</code>.',
    'focus' => 'Bare option: adds <code>shadowrootdelegatesfocus</code>.' ];

  $tagSee     = [ 'slot', 'parms', 'source' ];

?>
