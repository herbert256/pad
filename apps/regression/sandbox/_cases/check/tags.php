<?php

  // Tag behaviour carried over from check: case, if, switch and the until forms.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'tags/case_1',
      <<<'PAD'
      <p>
        {case $language}
          {when 'NL'}
            Hallo Wereld
          {when 'FR'}
            Bonjour le Monde
          {when 'DE'}
            Hallo Welt
          @else@
            Hello World
        {/case}
      </p>
      PAD,
      '<p>Bonjour le Monde</p>',
      [
        'language' => 'FR'
      ] ],

    [ 'tags/case_2',
      <<<'PAD'
      <p>
        {case $happiness * $hours}
          {when ($beer*2) + ($sausage*1)}
            Bonjour le Monde
          {when ($beer*4) + ($sausage*2)}
            Hallo Wereld
          {when ($beer*8) + ($sausage*4)}
            Hallo Welt
          @else@
            Hello World
        {/case}
      </p>
      PAD,
      '<p>Hallo Welt</p>',
      [
        'happiness' => 50,
        'hours' => 2,
        'beer' => 10,
        'sausage' => 5
      ] ],

    [ 'tags/if_1',
      <<<'PAD'
      <p>
        
        {if 5 gt 3}
            yes
          @else@
            no
        {/if}
        
        {if 3 gt 5}
            no
          @else@
            yes
        {/if}
        
      </p>
      PAD,
      '<p>yesyes</p>' ],

    [ 'tags/if_2',
      <<<'PAD'
      <p>
        {if $fruit eq 'banana'}
          {if $color eq 'pink'}
            <b> A pink banana </b>
          @else@
            <b> Not a pink banana </b>
          {/if}
        @else@
          {if $color eq 'green'}
            <b> An {$fruit} is {$color}</b>
          @else@
            <b> Not a green fruit </b>
          {/if}
        {/if}
      </p>
      PAD,
      '<p><b> An apple is green</b></p>',
      [
        'fruit' => 'apple',
        'color' => 'green'
      ] ],

    [ 'tags/switch',
      <<<'PAD'
      <table border="1">
        {users}
          <tr bgcolor="{switch 'pink' , 'orange'}">
            <td>{$name}</td>
            <td>{$phone}</td>
          </tr>
        {/users}
      </table>
      PAD,
      '<table border="1"><tr bgcolor="pink"><td>bob</td><td>555-3425</td></tr><tr bgcolor="orange"><td>jim</td><td>555-4364</td></tr><tr bgcolor="pink"><td>joe</td><td>555-3422</td></tr><tr bgcolor="orange"><td>jerry</td><td>555-4973</td></tr></table>',
      [
        'users' => [
          [
            'name' => 'bob',
            'phone' => '555-3425'
          ],
          [
            'name' => 'jim',
            'phone' => '555-4364'
          ],
          [
            'name' => 'joe',
            'phone' => '555-3422'
          ],
          [
            'name' => 'jerry',
            'phone' => '555-4973'
          ]
        ]
      ] ],

    [ 'tags/until_after',
      <<<'PAD'
      {set $abc = 11}
      {until}
        {$abc}
        {set $abc = $abc + 1}
      {/until $abc GT 10}
      PAD,
      '11' ],

    [ 'tags/until_before',
      <<<'PAD'
      {set $abc = 1}
      {until $abc GT 10}
        {$abc}
        {increment $abc}
      {/until}
      PAD,
      '12345678910' ],

    [ 'tags/while_after',
      <<<'PAD'
      {set $abc = 11}
      {while}
        {$abc}
        {set $abc = $abc + 1}
      {/while $abc LE 10}
      PAD,
      '11' ],

    [ 'tags/while_before',
      <<<'PAD'
      {set $abc = 1}
      {while $abc LE 10}
        {$abc}
        {set $abc = $abc + 1}
      {/while}
      PAD,
      '12345678910' ],

  ];

?>