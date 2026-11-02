<?php

  $tagAbout   = 'Writes a custom tag in the template itself - what a _tags/name.pad file is, without the file.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:define 'name'} ... {#parm} ... @content@ ... {/pad:define}
{pad:define 'name'} {pad:parms title, tone='info'/} ... {pad:slot 'footer'}@content@{/pad:slot} {/pad:define}
{define:name parm='value'/}
{define:name parm='value'} content {pad:slot 'footer'}fill{/pad:slot} {/define:name}
{define:name .../}
PAD;

  $tagParms   = [
    'name' => 'The name of the new tag, called with the required <code>define:</code> prefix. The same name can exist in other namespaces.' ];

  $tagOptions = [];

  $tagSee     = [ 'macro', 'parms', 'slot', 'content' ];

?>
