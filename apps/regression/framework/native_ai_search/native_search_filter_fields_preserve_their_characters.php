<?php

  $GLOBALS ['nativeSearchRows'] = [ [ 'id' => 1, 'title' => 'apple', 'kind' => 'A&B Å' ], [ 'id' => 2, 'title' => 'apple', 'kind' => 'other' ] ];
  $GLOBALS ['nativeFilterKind'] = 'A&B Å';
  $source = <<<'PAD'
{search:en 'nativeSearchRows', q='apple', fields='title', filters='{"kind":"{$nativeFilterKind}"}'}{$id}{/search:en}
PAD;
  $text = trim ( padUnprotect ( padUnescape ( padCode ( $source ) ) ) );
  $answer = $text === '1' ? 'native search filter fields retain ampersands and Unicode' : 'FAIL: ' . $text;

?>
