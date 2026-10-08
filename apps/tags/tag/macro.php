<?php

  $tagAbout   = 'A template function: declared parameters, given in order or by name, read as fields of its body.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{macro 'name', parm, parm='default'} ... {/macro}
{name 'value', 'value'}
{name parm='value'} ... {/name}
PAD;

  $tagParms   = [
    'name'  => 'The name the macro is called by from here on. Not the name of a built-in, application or <code>_common</code> tag, nor of a <code>{define}</code>.',
    'parms' => 'The parameters after the name: a name alone is required, <code>name=default</code> is optional - the default evaluated where the macro is used.' ];

  $tagOptions = [];

  $tagSee     = [ 'define', 'parms', 'set' ];

?>
