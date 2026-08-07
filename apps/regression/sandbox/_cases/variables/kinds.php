<?php

  // The leading character of a variable tag decides where the value is looked up. level/var.php
  // renders all of them without opening a level:
  //
  //   $  the field                          %  its occurrence form, read back as $
  //   !  the same field, raw                #  a parameter or option of the tag
  //   &  a property of the tag              ?  the field as a url parameter
  //
  // # and & have their own cases in tags/parameters.php. The two here are the ones the manual's
  // "Variable kinds" page lists and nothing covered: ? which builds a query fragment, and $$
  // which reads the name out of another variable first.
  //
  // The optional pipe belongs with them: a name that does not resolve ends the request, and this
  // is the one thing that makes it render as nothing instead.

  return [

    [ '? builds a url query fragment from the field',
      <<<'PAD'
      {set $t = 'a b'/}
      {?t}
      PAD,
      '&t=a+b' ],

    [ '$$ takes the name from another variable',
      <<<'PAD'
      {set $a = 'b'/}
      {set $b = 'deep'/}
      {$$a}
      PAD,
      'deep' ],

    [ 'a name that resolves to nothing is an error unless it is optional',
      <<<'PAD'
      {$nosuch | optional}
      ok
      PAD,
      'ok' ],

    [ 'the name option renames the level, and the property follows it',
      <<<'PAD'
      {pad name='myTag'}
        {&name}
      {/pad}
      PAD,
      'myTag' ],

  ];

?>