<?php

  $tagAbout   = 'A template function: declared parameters, given in order or by name, read as fields inside its body.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:macro 'name', required, optional='default'} ... {$required} {$optional} ... {/pad:macro}
{macro:name 'value'/}
{macro:name 'value', 'other'/}
{macro:name required='value', optional=$x/}
{macro:name 'value'} content for @content@ {/macro:name}
{macro:name .../}
PAD;

  $tagParms   = [
    'name'       => 'The name of the macro, called with the required <code>macro:</code> prefix. The same name can exist in other namespaces.',
    'parameters' => 'The items after the name declare the macro\'s parameters: a name alone is required, <code>name=default</code> is optional - the default is evaluated where the macro is used. A parameter may not be named like an option every tag takes (<code>sort</code>, <code>first</code>, <code>content</code> ...).' ];

  $tagOptions = [];

  $tagSee     = [ 'define', 'parms', 'slot', 'set' ];

?>
