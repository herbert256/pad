<?php

  // Pages carried over from check/deep.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'deep/five',
      <<<'PAD'
      {code}
        {six}
      {/code}
      PAD,
      '{six}' ],

  ];

?>