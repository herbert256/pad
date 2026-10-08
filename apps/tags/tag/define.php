<?php

  $tagAbout   = 'Writes a custom tag in the template itself - what a _tags/name.pad file is, without the file.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{define 'name'} ... {#parm} ... @content@ ... {/define}
{define 'name'} {parms title, tone='info'} ... {slot 'footer'}@content@{/slot} {/define}
{name parm='value'}
{name parm='value'} content {slot 'footer'}fill{/slot} {/name}
{define:name ...}
PAD;

  $tagParms   = [
    'name' => 'The name of the new tag. Under the strict check it may not be a built-in tag, an application tag or a <code>{macro}</code> already.' ];

  $tagOptions = [];

  $tagSee     = [ 'macro', 'parms', 'slot', 'content' ];

?>
