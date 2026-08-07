<?php

  // How PHP and PAD meet: what a page's .php half hands to its template, and how the template
  // reads it.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'php_and_html/z01',
      <<<'PAD'
      <h3>Hello World</h3>
      PAD,
      '<h3>Hello World</h3>' ],

    [ 'php_and_html/z05',
      <<<'PAD'
      <h3>{$hi}</h3>
      PAD,
      '<h3>Hello World</h3>',
      [
        'hi' => 'Hello World'
      ] ],

    [ 'php_and_html/z06',
      <<<'PAD'
      {hi}
        <h3>Hello World {$hi}</h3>
      {/hi}
      PAD,
      '<h3>Hello World 1</h3><h3>Hello World 2</h3><h3>Hello World 3</h3>',
      [
        'hi' => [
          [
            'hi' => 1
          ],
          [
            'hi' => 2
          ],
          [
            'hi' => 3
          ]
        ]
      ] ],

    [ 'php_and_html/z25',
      <<<'PAD'
      <h3>Hello World 3</h3>
      
      @start@
        <h3>Hello World {$hi}</h3>
      @end@
      
      <h3>{$hi}</h3>
      
      <h3>Hello World 8 </h3>
      PAD,
      '<h3>Hello World 3</h3>@start@<h3>Hello World Hello World 7</h3>@end@<h3>Hello World 7</h3><h3>Hello World 8 </h3>',
      [
        'hi' => 'Hello World 7'
      ] ],

  ];

?>