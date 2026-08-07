<?php

  // The odds and ends check keeps together: assignment, scope, bool, eval and the rest.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'miscellaneous/assign',
      <<<'PAD'
      {set $abc = '1234567890'}
      {$abc}
      
      <hr>
      
      {set $abc = '1234567890'}
      {$abc | . ' after' | 'before ' . }
      
      <hr>
      
      {set $abc = '1234567890'}
      {$abc | @ . ' after' | 'before ' . @}
      
      <hr>
      
      {set $abc = '1234567890'}
      {$abc | . '"'}<br>
      {$abc | '"' . }<br>
      {$abc | . '"' . }
      
      <hr>
      PAD,
      '1234567890<hr>before 1234567890 after<hr>before 1234567890 after<hr>1234567890&quot;<br>&quot;1234567890<br>1234567890&quot;1234567890<hr>' ],

    [ 'miscellaneous/bool',
      <<<'PAD'
      {bool 'abc'}
          true
      {/bool}
      
      {bool 'xyz'}  
      
      {/bool}
      
      <h2>abc = waar</h2>
      
      {abc}
        ja
      @else@
        nee
      {/abc}
      <hr>
      
      <h2>xyz = niet waar</h2>
      
      {xyz}
        ja
      @else@
        nee
      {/xyz}
      <hr>
      PAD,
      '<h2>abc = waar</h2>ja<hr><h2>xyz = niet waar</h2>nee<hr>' ],

    [ 'miscellaneous/in',
      <<<'PAD'
      {if 5 in [7,9,4,2,9,6]}
        NOK
      @else@
        ok
      {/if}
      
      {if 4 in [7,9,4,2,9,6]}
        ok
      @else@
        NOK
      {/if}
      PAD,
      'okok' ],

    [ 'miscellaneous/other',
      <<<'PAD'
      <h3>part 1</h3>  
      {set $abc = (15 gt 33) } eq {$abc}
      
      
      <h3>part 3</h3>
      
      {if ( PHP_EOL EQ "\n" ) and ( PHP_EOL EQ 0x0a) }
        ok
      @else@
        NOK
      {/if}
      
      <h3>part 4</h3>
      
      {set $abc = ''}
      {set $klm = 0}
      {set $xyz = 'ok'}
      
      <h3>part 6</h3>
      
      {set $abc = 'abc'}
      {set $xyz = 'xyz'}
      
      {$abc | mid (2,1)}
      {$abc | mid ( 2 , 1 ) }
      
      <h3>part 10</h3>
      
      {if 5 gt 3}
        ok
      @else@
        NOK
      {/if}
      
      {if 3 gt 5}
        NOK
      @else@
        ok
      {/if}
      
      {if 5 gt 3 and 7 gt 6}
        ok
      @else@
        NOK
      {/if}
      
      
      {if 3 gt 5 or 6 gt 7}
        NOK
      @else@
        ok
      {/if}
      
      
      {if 1 gt 1 and  1 gt 1 and 1 gt 1 and 1 gt 1 and 1 gt 1 }
        NOK
      @else@
        ok
      {/if}
      
      {if 1 gt 1 and  1 gt 1 and 1 gt 1 and 1 gt 1 or 5 gt 4 }
        ok
      @else@
        NOK
      {/if}
      
      
      {if (1 gt 1) or ( 1 gt 1 or ( 1 gt 1 or (1 gt 1 or (5 gt 4) )  )  ) }
        ok
      @else@
        NOK
      {/if}
      
      {if (1 gt 1) or ( 1 gt 1 or ( 1 gt 1 or (1 gt 1 or (5 gt 6) )  )  ) }
        NOK
      @else@
        ok
      {/if}
      
      <h3>part 11c</h3>
      
      {if 3 gt 5 or 6 gt 7}
        NOK
      @else@
        ok
      {/if}
      {if 3 gt 5 or 6 gt 7}
        NOK
      @else@
        ok
      {/if}
      {if 3 gt 5 or 6 gt 7}
        NOK
      @else@
        ok
      {/if}
      PAD,
      '<h3>part 1</h3>  eq <h3>part 3</h3>ok<h3>part 4</h3><h3>part 6</h3>bb<h3>part 10</h3>okokokokokokokok<h3>part 11c</h3>okokok' ],

    [ 'miscellaneous/red',
      <<<'PAD'
      <hoi>
      PAD,
      '<hoi>' ],

    [ 'miscellaneous/scope',
      <<<'PAD'
      test
      PAD,
      'test' ],

    [ 'miscellaneous/set_1',
      <<<'PAD'
      {set $abc = 123}
      
      {$abc}
      PAD,
      '123' ],

    [ 'miscellaneous/set_2',
      <<<'PAD'
      {set $abc = 123}
      
      {$abc}
      
      {pad $abc = 456 }
          {$abc}
      {/pad}
      
      {$abc}
      PAD,
      '123456123' ],

    [ 'miscellaneous/set_3',
      <<<'PAD'
      {set $abc = 123}
      
      {$abc}
      
      {pad $abc = 456}
          {$abc}
          {set $abc = 789}
          {$abc}
      {/pad}
      
      {$abc}
      PAD,
      '123456789123' ],

    [ 'miscellaneous/set_4',
      <<<'PAD'
      {pad $abc = 1}
       {pad $abc = 2}
        {pad $abc = 3}
         {pad $abc = 4}
          {pad $abc = 5}
           {pad $abc = 6}
            {pad $abc = 7}
             {pad $abc = 8}
              {pad $abc = 9}
               {$abc}
              {/pad}
              {$abc}
             {/pad}
             {$abc}
            {/pad}
            {$abc}
           {/pad}
           {$abc}
          {/pad}
          {$abc}
         {/pad}
         {$abc}
        {/pad}
        {$abc}
       {/pad}
       {$abc}
      {/pad}
      PAD,
      '987654321' ],

    [ 'miscellaneous/var',
      <<<'PAD'
      <p>2 - {$$hi}</p>
      
      <p>3 - {${$hi}}</p>
      PAD,
      '<p>2 - Hello World</p><p>3 - Hello World</p>',
      [
        'hey' => 'Hello World',
        'hi' => 'hey'
      ] ],

  ];

?>