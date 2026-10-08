<?php

  $tagAbout   = 'A template function: declared parameters, given in order or by name, read as fields inside its body.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{macro 'name', required, optional='default'} ... {$required} {$optional} ... {/macro}
{name 'value'}
{name 'value', 'other'}
{name required='value', optional=$x}
{name 'value'} content for @content@ {/name}
{macro:name ...}
PAD;

  $tagParms   = [
    'name'       => 'The name of the macro, used as a tag from here on. Under the strict check it may not be a built-in tag, an application tag or a <code>{define}</code> already.',
    'parameters' => 'The items after the name declare the macro\'s parameters: a name alone is required, <code>name=default</code> is optional - the default is evaluated where the macro is used. A parameter may not be named like an option every tag takes (<code>sort</code>, <code>first</code>, <code>content</code> ...).' ];

  $tagOptions = [];

  $tagSee     = [ 'define', 'parms', 'slot', 'set' ];

?>
