<?php

  // The else construct as the manual presents it.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'constructs/else_4',
      <<<'PAD'
      {myVar}
        true
      @else@
        false
      {/myVar}
      PAD,
      'false',
      [
        'myVar' => FALSE
      ] ],

  ];

?>