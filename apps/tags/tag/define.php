<?php

  $tagAbout   = 'A custom tag written in the template itself - what an _tags/name.pad file is, without the file.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{define 'name'} ... {/define}
{name} ... {/name}
{name parm='value'}
PAD;

  $tagParms   = [
    'name' => 'The name the tag is used by from here on. Not the name of a built-in, application or <code>_common</code> tag, nor of a <code>{macro}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'macro', 'parms', 'slot', 'content' ];

?>
