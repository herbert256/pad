<?php

  // The ten ways of showing one table that the manual's "3 ways to make a table" page walks
  // through, which is where they are embedded from.
  //
  // They used to live in the check application and the manual reached into it for them with
  // app='check'. They are the manual's own now, under manual/tableFun/. Order matters between
  // them: fun_0 is nothing but the {content 'cell'} definition the rest print through, so the
  // page embeds it first and each case here restates it.
  //
  // Each case is the page as the manual renders it, with the answer it is supposed to give
  // stated here instead of left to a stored copy of the HTML.

  return [

    [ 'tableFun/fun_0',
      <<<'PAD'
      {content 'cell'} 
        <td {if $cell EQ 23} bgcolor="red" {/if} >
          {$cell}
        </td>
      {/content}
      PAD,
      '' ],

    [ 'tableFun/fun_1_a',
      <<<'PAD'
      <table border='1'>
        {rows}
          <tr>
            {cols}
              {cell}  
            {/cols}
          </tr>
        {/rows}
      </table>
      PAD,
      '<table border=\'1\'><tr>11  12  13  14  15  </tr><tr>21  22  23  24  25  </tr><tr>31  32  33  34  35  </tr></table>',
      [
        'row' => 4,
        'col' => 6,
        'rows' => [
          '1' => [
            'cols' => [
              '1' => [
                'cell' => 11
              ],
              '2' => [
                'cell' => 12
              ],
              '3' => [
                'cell' => 13
              ],
              '4' => [
                'cell' => 14
              ],
              '5' => [
                'cell' => 15
              ]
            ]
          ],
          '2' => [
            'cols' => [
              '1' => [
                'cell' => 21
              ],
              '2' => [
                'cell' => 22
              ],
              '3' => [
                'cell' => 23
              ],
              '4' => [
                'cell' => 24
              ],
              '5' => [
                'cell' => 25
              ]
            ]
          ],
          '3' => [
            'cols' => [
              '1' => [
                'cell' => 31
              ],
              '2' => [
                'cell' => 32
              ],
              '3' => [
                'cell' => 33
              ],
              '4' => [
                'cell' => 34
              ],
              '5' => [
                'cell' => 35
              ]
            ]
          ]
        ]
      ] ],

    [ 'tableFun/fun_1_b',
      <<<'PAD'
      <table border='1'>
        {rows}
          <tr>
            {cols}
              {cell}  
            {/cols}
          </tr>
        {/rows}
      </table>
      PAD,
      '<table border=\'1\'><tr>11  12  13  14  15  </tr><tr>21  22  23  24  25  </tr><tr>31  32  33  34  35  </tr></table>',
      [
        'rows' => [
          '1' => [
            'cols' => [
              '1' => [
                'cell' => 11
              ],
              '2' => [
                'cell' => 12
              ],
              '3' => [
                'cell' => 13
              ],
              '4' => [
                'cell' => 14
              ],
              '5' => [
                'cell' => 15
              ]
            ]
          ],
          '2' => [
            'cols' => [
              '1' => [
                'cell' => 21
              ],
              '2' => [
                'cell' => 22
              ],
              '3' => [
                'cell' => 23
              ],
              '4' => [
                'cell' => 24
              ],
              '5' => [
                'cell' => 25
              ]
            ]
          ],
          '3' => [
            'cols' => [
              '1' => [
                'cell' => 31
              ],
              '2' => [
                'cell' => 32
              ],
              '3' => [
                'cell' => 33
              ],
              '4' => [
                'cell' => 34
              ],
              '5' => [
                'cell' => 35
              ]
            ]
          ]
        ]
      ] ],

    [ 'tableFun/fun_2_a',
      <<<'PAD'
      <table border='1'> 
        {fun}
         <tr>
            {fun}
              {cell $fun}  
            {/fun}
          </tr>
        {/fun}
      </table>
      PAD,
      '<table border=\'1\'> <tr>{cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  </tr><tr>{cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  </tr><tr>{cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  {cell $fun}  </tr></table>',
      [
        'fun' => [
          [
            11,
            12,
            13,
            14,
            15
          ],
          [
            21,
            22,
            23,
            24,
            25
          ],
          [
            31,
            32,
            33,
            34,
            35
          ]
        ]
      ] ],

    [ 'tableFun/fun_3_a',
      <<<'PAD'
      <table border='1'>
        {row}
          <tr>
            {column}
              {cell ($row*10) + $column} 
            {/column}
          </tr>
        {/row}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr></table>',
      [
        'row' => [
          1,
          2,
          3
        ],
        'column' => [
          1,
          2,
          3,
          4,
          5
        ]
      ] ],

    [ 'tableFun/fun_3_b',
      <<<'PAD'
      {data 'row'   } [1,2,3]     {/data}
      {data 'column'} [1,2,3,4,5] {/data}
      
      <table border='1'>
        {row}
          <tr>
            {column}
              {cell ($row*10) + $column} 
            {/column}
          </tr>
        {/row}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr><tr>{cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} {cell ($row*10) + $column} </tr></table>' ],

    [ 'tableFun/fun_5_a',
      <<<'PAD'
      <table border='1'>
        {sequence '10..30', increment=10}
          <tr>
            {sequence '1..5'}
              {cell $-2 + $-1}
            {/sequence}
          </tr>
        {/sequence}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}</tr><tr>{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}</tr><tr>{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}{cell $-2 + $-1}</tr></table>' ],

    [ 'tableFun/fun_5_b',
      <<<'PAD'
      <table border='1'>
        {sequence '10..30', increment=10, name='row'}
          <tr>
            {sequence '1..5', name='column'}
              {cell $row + $column}
            {/sequence}
          </tr>
        {/sequence}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}</tr><tr>{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}</tr><tr>{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}{cell $row + $column}</tr></table>' ],

    [ 'tableFun/fun_8',
      <<<'PAD'
      <table border='1'>
        {set $row = 10}
        {while $row LE 30}
          <tr>
            {set $col = 1}
            {while $col LE 5}
              {cell $row + $col}
              {increment $col}
            {/while}
          </tr>
          {set $row = $row + 10}
        {/while}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr></table>' ],

    [ 'tableFun/fun_9',
      <<<'PAD'
      <table border='1'>
        {row}
          <tr>
            {set $col = 1}
            {while $col LE 5}
              {cell $row + $col}
              {increment $col}
            {/while}
          </tr>
        {/row}
      </table>
      PAD,
      '<table border=\'1\'><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr><tr>{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}{cell $row + $col}</tr></table>',
      [
        'row' => [
          10,
          20,
          30
        ]
      ] ],

  ];

?>