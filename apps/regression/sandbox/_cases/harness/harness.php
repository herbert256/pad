<?php

  // The runner tested by the runner: the helpers every suite verdict passes through - url
  // building, test counting, comparison masking, the coverage patterns - are the one layer
  // the thousand assertions elsewhere do not touch, and a defect here turns into a wrong
  // verdict rather than a red row. These cases call them directly, with 'scope' so the
  // page's own function space is theirs.
  //
  // Machine-specific parts - the host, the filesystem - are cut off with after/afterLast
  // before comparing, so the cases hold anywhere the suite runs. The two pattern cases end
  // in a stray dot on purpose: a slash-delimited expected value is read as a regex by the
  // suite's own convention, which is exactly the kind of thing this group exists to know. The two pattern cases end
  // in a stray dot on purpose: a slash-delimited expected value is read as a regex by the
  // suite's own convention, which is exactly the kind of thing this group exists to know.

  return [

    [ 'a test url carries padInclude',
      <<<'PAD'
      {echo '' | php:getPagesUrl('regression2', 'pairing') | after('?')}
      PAD,
      'pairing&padInclude',
      'scope' ],

    [ 'the root index is the one test fetched full',
      <<<'PAD'
      {echo '' | php:getPagesUrl('regression3', 'index') | after('?')}
      PAD,
      'index',
      'scope' ],

    [ 'the answer lives beside the page',
      <<<'PAD'
      {echo '' | php:getPagesWantFile('regression2', 'pairing') | afterLast('/')}
      PAD,
      'pairing.txt',
      'scope' ],

    [ 'a catalogue page counts its labelled lines',
      <<<'PAD'
      {echo '' | php:getPagesCount('catalog/x', $harnessLabelled)}
      PAD,
      '2',
      'scope' ],

    [ 'an ordinary page counts one however long its answer',
      <<<'PAD'
      {echo '' | php:getPagesCount('misc/x', $harnessLabelled)}
      PAD,
      '1',
      'scope' ],

    [ 'a rendering counts one even in the catalogue',
      <<<'PAD'
      {echo '' | php:getPagesCount('catalog/x', $harnessRendering)}
      PAD,
      '1',
      'scope' ],

    [ 'the comparison strips the session and request ids',
      <<<'PAD'
      {echo '' | php:getRegressionCompare('a padSesID=Zx9 b padReqID=Qq1 c')}
      PAD,
      'a padSesID= b padReqID= c',
      'scope' ],

    [ 'draw masking turns digits into one mark',
      <<<'PAD'
      {echo '' | php:getRegressionCompare('rows 12 34 56', 1)}
      PAD,
      'rows #',
      'scope' ],

    [ 'draw masking takes the day and month names too',
      <<<'PAD'
      {echo '' | php:getRegressionCompare('Friday 8 August', 1)}
      PAD,
      'DAY # MONTH',
      'scope' ],

    [ 'draw masking collapses a demo result whole',
      <<<'PAD'
      {echo '' | php:getRegressionCompare('<!-- START DEMO RESULT -->7 of 9<!-- END DEMO RESULT -->', 1) | html}
      PAD,
      '&lt;!-- START DEMO RESULT --&gt;#&lt;!-- END DEMO RESULT --&gt;',
      'scope' ],

    [ 'a page declares that it draws with one word',
      <<<'PAD'
      {echo '' | php:getRegressionDraws('drawn at random')}.{echo '' | php:getRegressionDraws('plain page')}.
      PAD,
      '1..',
      'scope' ],

    [ 'a declared error code is expected, an undeclared one is not',
      <<<'PAD'
      {echo '' | php:getRegressionExpects('regression3', 'error/pad_1', '500')}.{echo '' | php:getRegressionExpects('regression3', 'error/pad_1', '404')}.
      PAD,
      '1..',
      'scope' ],

    [ 'coverage matches a tag by its brace',
      <<<'PAD'
      {echo '' | php:getReferencePattern('tidy', 'tag/pad')}.
      PAD,
      '/\{\/?tidy\b/.',
      'scope' ],

    [ 'coverage matches an option by its spelling',
      <<<'PAD'
      {echo '' | php:getReferencePattern('tidy', 'options/general')}.
      PAD,
      '/\btidy\s*[=,}]/.',
      'scope' ],

    [ 'a stored case is restored to what the template said',
      <<<'PAD'
      {echo '' | php:getReferenceText('<b>&open;x&close; a&at;b c&pipe;d</b>') | ignore}
      PAD,
      '{x} a@b c|d',
      'scope' ],

  ];

?>
