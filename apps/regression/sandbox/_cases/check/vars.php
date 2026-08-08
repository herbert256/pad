<?php

  // Variable resolution carried over from check, including the @ reference forms.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.
  //
  // Where check wrapped its examples in {table}{demo} to show source beside result, that
  // scaffolding is stripped: it is presentation from the _common application, not behaviour
  // under test, and the demos of one page share their data so they stay one case.

  return [

    [ 'vars/at/5',
      <<<'PAD'
      {pad name='cool', abc=123, 'fiets', $abc='banaan', $go='nuts', xyz=789, 12*15, 34}
      
      <h3>variables</h3>
      
        1 - {$go} <br>
        2 - {@go} <br>
        3 - {$go@cool} <br>
        4 - {$go@cool:variables} <br>
        5 - {$go@variables} <br>
      
      <br>
      
        1 - {$<@variables} <br>
        2 - {$<@cool:variables} <br>
      
      <br>
      
        1 - {$>@variables} <br>
        2 - {$>@cool:variables} <br>
      
      <br>
      
        <table border="1">
          <th>name</th>
          <th>value</th>
          {variables@cool}
            <tr>
              <td>{$name}</td>
              <td>{$value}</td>
            </tr>
          {/variables@cool}
        </table>
        
      {/pad}
      PAD,
      '<h3>variables</h3>1 - nuts <br>2 - {@go} <br>3 - nuts <br>4 - nuts <br>5 - nuts <br><br>1 - banaan <br>2 - banaan <br><br>1 - nuts <br>2 - nuts <br><br><table border="1"><th>name</th><th>value</th><tr><td>abc</td><td>banaan</td></tr><tr><td>go</td><td>nuts</td></tr></table>' ],

    [ 'vars/at/5a',
      <<<'PAD'
      {pad name='abc', abc=123, 'fiets', $abc='banaan', $go='nuts-1', xyz=789, 12*15, 34, $x1=123}
      
        {pad name='klm', abc=123, 'fiets', $abc='banaan', $go='nuts-2', xyz=789, 12*15, 34, $x2=456}
      
          {pad name='xyz', abc=123, 'fiets', $abc='banaan', $go='nuts-3', xyz=789, 12*15, 34, $x3=789}
      
        1 - {x1@} <br>
        2 - {x2@} <br>
        3 - {x3@} <br>
      
        <br>
      
        1 - {$x1@abc} <br>
        2 - {$x2@klm} <br>
        3 - {$x3@xyz} <br>
      
        <br>
      
        1 - {$x1@variables} <br>
        1 - {$x2@variables} <br>
        1 - {$x3@variables} <br>
      
        <br>
      
        1 - {$>@abc:variables} <br>
        2 - {$>@klm:variables} <br>
        2 - {$>@xyz:variables} <br>
      
        <br>
      
        <table border="1">
          <th>name</th><th>value</th>
          {variables@abc}
            <tr><td>{$name}</td><td>{$value}</td></tr>
          {/variables@abc}
        </table>
        
        <br>
      
        <table border="1">
          <th>name</th><th>value</th>
          {variables@klm}
            <tr><td>{$name}</td><td>{$value}</td></tr>
          {/variables@klm}
        </table>
        
        <br>
      
        <table border="1">
          <th>name</th><th>value</th>
          {variables@xyz}
            <tr><td>{$name}</td><td>{$value}</td></tr>
          {/variables@xyz}
        </table>
        
          {/pad}
      
        {/pad}
      
      {/pad}
      PAD,
      '1 - 123 <br>2 - 456 <br>3 - 789 <br><br>1 - 123 <br>2 - 456 <br>3 - 789 <br><br>1 - 123 <br>1 - 456 <br>1 - 789 <br><br>1 - 123 <br>2 - 456 <br>2 - 789 <br><br><table border="1"><th>name</th><th>value</th><tr><td>abc</td><td>banaan</td></tr><tr><td>go</td><td>nuts-1</td></tr><tr><td>x1</td><td>123</td></tr></table><br><table border="1"><th>name</th><th>value</th><tr><td>abc</td><td>banaan</td></tr><tr><td>go</td><td>nuts-2</td></tr><tr><td>x2</td><td>456</td></tr></table><br><table border="1"><th>name</th><th>value</th><tr><td>abc</td><td>banaan</td></tr><tr><td>go</td><td>nuts-3</td></tr><tr><td>x3</td><td>789</td></tr></table>' ],

    [ 'vars/at/10',
      <<<'PAD'
      {content 'myContent'}
        <table border=1 valign=center>
          <tr>
            <th>        </th>
            <th> first  </th>
            <th> border </th>
            <th> middle </th>
            <th> last   </th>
            <th> even   </th>
            <th> odd    </th>
          </tr>
          @content@
        </table>
      {/content}
      
      {myContent}
        {staff}
          <tr>
            <td> {$name}                          </td>
            <td> {first@staff}  X {/first@staff}  </td>
            <td> {border@staff} X {/border@staff} </td>
            <td> {middle@staff} X {/middle@staff} </td>
            <td> {last@staff}   X {/last@staff}   </td>
            <td> {even@staff}   X {/even@staff}   </td>
            <td> {odd@staff}    X {/odd@staff}    </td>
          </tr>
        {/staff}
      {/myContent}
      
      {myContent}
        {staff}
        	{true}
      	    <tr>
      	      <td> {$name}                     </td>
      	      <td> {first@-2}  X {/first@-2}  </td>
      	      <td> {border@-2} X {/border@-2} </td>
      	      <td> {middle@-2} X {/middle@-2} </td>
      	      <td> {last@-2}   X {/last@-2}   </td>
      	      <td> {even@-2}   X {/even@-2}   </td>
      	      <td> {odd@-2}    X {/odd@-2}    </td>
      	    </tr>
          {/true}
        {/staff}
      {/myContent}
      
      {myContent}
        {staff}
        	<tr>
            <td> {$name}                </td>
            <td> {first@}  X {/first@}  </td>
            <td> {border@} X {/border@} </td>
            <td> {middle@} X {/middle@} </td>
            <td> {last@}   X {/last@}   </td>
            <td> {even@}   X {/even@}   </td>
            <td> {odd@}    X {/odd@}    </td>
          </tr>
        {/staff}
      {/myContent}
      PAD,
      '<table border=1 valign=center><tr><th>        </th><th> first  </th><th> border </th><th> middle </th><th> last   </th><th> even   </th><th> odd    </th></tr><tr><td> joe                          </td><td>   X   </td><td>  X  </td><td>  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jim                          </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> john                          </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jack                          </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> jerry                          </td><td>   </td><td>  X  </td><td>  </td><td>    X    </td><td>    </td><td>     X     </td></tr></table><table border=1 valign=center><tr><th>        </th><th> first  </th><th> border </th><th> middle </th><th> last   </th><th> even   </th><th> odd    </th></tr><tr><td> joe                     </td><td>   X   </td><td>  X  </td><td>  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jim                     </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> john                     </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jack                     </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> jerry                     </td><td>   </td><td>  X  </td><td>  </td><td>    X    </td><td>    </td><td>     X     </td></tr></table><table border=1 valign=center><tr><th>        </th><th> first  </th><th> border </th><th> middle </th><th> last   </th><th> even   </th><th> odd    </th></tr><tr><td> joe                </td><td>   X   </td><td>  X  </td><td>  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jim                </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> john                </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    </td><td>     X     </td></tr><tr><td> jack                </td><td>   </td><td>  </td><td>  X  </td><td>    </td><td>    X    </td><td>     </td></tr><tr><td> jerry                </td><td>   </td><td>  X  </td><td>  </td><td>    X    </td><td>    </td><td>     X     </td></tr></table>' ],

    [ 'vars/at/3',
      <<<'PAD'
      {sequence '25..100', name='myRange'} 
      
      {$myRange.<} <br> 
      {$myRange.<1} <br> 
      {$myRange.<2} <br> 
      {$myRange.<3} <br> 
      
      {$myRange.>} <br> 
      {$myRange.>1} <br> 
      {$myRange.>2} <br> 
      {$myRange.>3} <br> 
      
      {$myRange.<3}          <br> 
      {$<3@myRange}          <br> 
      {$myRange.<3@sequence} <br> 
      {$<3@sequence:myRange} <br>
      PAD,
      ' 25 <br> 25 <br> 26 <br> 27 <br> 100 <br> 100 <br> 99 <br> 98 <br> 27          <br> 27          <br> 27 <br> 27 <br>' ],

    [ 'vars/at/4',
      <<<'PAD'
      111
      113
      <br>
      {$a.b.1.c.1.<}
      {$a.b.1.c.1.>}
      
      111
      113
      <br>
      {$a.b.<.c.<.<}
      {$a.b.<.c.<.>}
      
      331
      333
      <br>
      {$a.b.>.c.>.<}
      {$a.b.>.c.>.>}
      PAD,
      '111113<br>111113111113<br>111113331333<br>331333',
      [
        'a' => [
          'b' => [
            '1' => [
              'c' => [
                '1' => [
                  '1' => 111,
                  '2' => 112,
                  '3' => 113
                ],
                '2' => [
                  '1' => 121,
                  '2' => 122,
                  '3' => 123
                ],
                '3' => [
                  '1' => 131,
                  '2' => 132,
                  '3' => 133
                ]
              ]
            ],
            '2' => [
              'c' => [
                '1' => [
                  '1' => 211,
                  '2' => 212,
                  '3' => 213
                ],
                '2' => [
                  '1' => 221,
                  '2' => 222,
                  '3' => 223
                ],
                '3' => [
                  '1' => 231,
                  '2' => 232,
                  '3' => 233
                ]
              ]
            ],
            '3' => [
              'c' => [
                '1' => [
                  '1' => 311,
                  '2' => 312,
                  '3' => 313
                ],
                '2' => [
                  '1' => 321,
                  '2' => 322,
                  '3' => 323
                ],
                '3' => [
                  '1' => 331,
                  '2' => 332,
                  '3' => 333
                ]
              ]
            ]
          ]
        ]
      ] ],
  ];

?>