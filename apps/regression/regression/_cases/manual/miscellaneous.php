<?php

  // The parse examples, and the odds and ends the manual keeps together.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'miscellaneous/parse/1',
      <<<'PAD'
      {pad myOption='{$myVar}', $xyz=789}
         ....
      {/pad}
      PAD,
      '....',
      [
        'myVar' => TRUE
      ] ],

    [ 'miscellaneous/parse/2',
      <<<'PAD'
      {pad myOption='ABC', $xyz=789}
         ....
      {/pad}
      PAD,
      '....' ],

    [ 'miscellaneous/parse/3',
      <<<'PAD'
      {pad myOption=ABC, $xyz=789}
         ....
      {/pad}
      PAD,
      '....' ],

  ];

?>