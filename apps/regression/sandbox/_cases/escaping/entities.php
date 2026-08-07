<?php

  // The PAD entities, which are how a template writes a character the parser would otherwise act
  // on. The manual's "Escaping special PAD chars" page lists six and none had a case:
  //
  //   &open;  {     &pipe;   |     &comma;  ,
  //   &close; }     &eq;     =     &at;     @
  //
  // Each is turned back into its character once the whole request is written, which is why the
  // suite runs padUnescape() over a case's output before comparing - a case then states what the
  // page shows rather than the spelling it passed through.
  //
  // The three that matter inside a tag are pipe, eq and comma: those are what padExplode splits a
  // parameter list on, so a value that contains one has to be written as the entity or the tag is
  // read as having more parameters than it has.

  return [

    [ 'the brace entities write the characters',
      <<<'PAD'
      &open;a&close;
      PAD,
      '{a}' ],

    [ 'a pipe inside a parameter is not a pipe',
      <<<'PAD'
      {echo 'a&pipe;b'}
      PAD,
      'a|b' ],

    [ 'nor is an equals sign',
      <<<'PAD'
      {echo 'a&eq;b'}
      PAD,
      'a=b' ],

    [ 'nor a comma',
      <<<'PAD'
      {echo 'a&comma;b'}
      PAD,
      'a,b' ],

    [ 'and an at is not a reference',
      <<<'PAD'
      {echo 'a&at;b'}
      PAD,
      'a@b' ],

    [ 'a comma written plainly does separate the parameters',
      <<<'PAD'
      {echo 'a','b'}
      PAD,
      'ab' ],

  ];

?>