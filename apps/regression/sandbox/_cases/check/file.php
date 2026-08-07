<?php

  // File tags carried over from check.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'file/done',
      <<<'PAD'
      Done: {$padFile}
      PAD,
      'Done: demo.txt',
      [
        'padFile' => 'demo.txt'
      ] ],

  ];

?>