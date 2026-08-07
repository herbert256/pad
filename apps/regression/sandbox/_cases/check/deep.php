<?php

  // Pages carried over from check/deep. The rest of that directory is pages/deep, where the
  // chain of {page} and {restart} calls it is really about can be a request.
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