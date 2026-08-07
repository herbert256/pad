<?php

  // The smallest pages there are, which is what makes them worth keeping.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

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

    [ 'hello/index',
      <<<'PAD'
      <h3>
        <font color="{$color}">
          {$hi}
        </font>
      </h3>
      PAD,
      '<h3><font color="black">Hello World !</font></h3>',
      [
        'color' => 'black',
        'hi' => 'Hello World !'
      ] ],

  ];

?>