<?php

  // The hello example the manual starts from.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'hello/hello',
      <<<'PAD'
      <h3>
        {$hi}
      </h3>
      PAD,
      '<h3>Hello World !</h3>',
      [
        'hi' => 'Hello World !'
      ] ],

  ];

?>