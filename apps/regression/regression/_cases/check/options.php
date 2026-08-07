<?php

  // Tag options carried over from check.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.
  //
  // Where check wrapped its examples in {table}{demo} to show source beside result, that
  // scaffolding is stripped: it is presentation from the _common application, not behaviour
  // under test, and the demos of one page share their data so they stay one case.

  return [

    [ 'options/optional',
      <<<'PAD'
      aaa
      
      {doesNotExists optional}
        123
      {/doesNotExists}
      
      {noNo optional}
      
      zzz
      PAD,
      'aaazzz' ],

    [ 'options/print',
      <<<'PAD'
      {data 'abc'}
        (1,2,3,4,5)
      {/data}
      
        {abc print}
      
        {abc print, open='(', close=')'}
      
        {abc print, glue=','}
      
        {abc print, open='(', glue=',', close=')'}
      
        {abc print, glue=',', quote="'"}
      
        {abc print, open='(', glue=',', quote="'", close=')'}
      PAD,
      '12345(12345)1,2,3,4,5(1,2,3,4,5)\'1\',\'2\',\'3\',\'4\',\'5\'(\'1\',\'2\',\'3\',\'4\',\'5\')' ],

    [ 'options/sort',
      <<<'PAD'
      {content 'myContent'}
        <table border=1>
          <tr>
            <th> volume   </th>
            <th> edition  </th>
            <th> file     </th>
          </tr>
          @start@
            <tr>
              <td> {$volume}   </td>
              <td> {$edition}  </td>
              <td> {$file}     </td>
            </tr>    
          @end@
        </table>
      {/content}
      
      {data 'myData'}
        [ { "volume": 55, "edition": 3, "file": "file1.xml"  },
          { "volume": 33, "edition": 1, "file": "file2.xml"  },
          { "volume": 55, "edition": 2, "file": "file11.xml" },
          { "volume": 33, "edition": 3, "file": "file12.xml" },
          { "volume": 55, "edition": 1, "file": "file21.xml" },
          { "volume": 33, "edition": 2, "file": "file8.xml"  } ]
      {/data}
      
      {myContent data='myData'}
      {myContent data='myData', sort='volume;edition'}
      {myContent data='myData', sort='volume DESC; edition DESC'}
      {myContent data='myData', sort='file'}
      {myContent data='myData', sort='file NATURAL'}
      PAD,
      '<table border=1><tr><th> volume   </th><th> edition  </th><th> file     </th></tr><tr><td> 55   </td><td> 3  </td><td> file1.xml     </td></tr>    <tr><td> 33   </td><td> 1  </td><td> file2.xml     </td></tr>    <tr><td> 55   </td><td> 2  </td><td> file11.xml     </td></tr>    <tr><td> 33   </td><td> 3  </td><td> file12.xml     </td></tr>    <tr><td> 55   </td><td> 1  </td><td> file21.xml     </td></tr>    <tr><td> 33   </td><td> 2  </td><td> file8.xml     </td></tr>    </table><table border=1><tr><th> volume   </th><th> edition  </th><th> file     </th></tr><tr><td> 33   </td><td> 1  </td><td> file2.xml     </td></tr>    <tr><td> 33   </td><td> 2  </td><td> file8.xml     </td></tr>    <tr><td> 33   </td><td> 3  </td><td> file12.xml     </td></tr>    <tr><td> 55   </td><td> 1  </td><td> file21.xml     </td></tr>    <tr><td> 55   </td><td> 2  </td><td> file11.xml     </td></tr>    <tr><td> 55   </td><td> 3  </td><td> file1.xml     </td></tr>    </table><table border=1><tr><th> volume   </th><th> edition  </th><th> file     </th></tr><tr><td> 55   </td><td> 3  </td><td> file1.xml     </td></tr>    <tr><td> 55   </td><td> 2  </td><td> file11.xml     </td></tr>    <tr><td> 55   </td><td> 1  </td><td> file21.xml     </td></tr>    <tr><td> 33   </td><td> 3  </td><td> file12.xml     </td></tr>    <tr><td> 33   </td><td> 2  </td><td> file8.xml     </td></tr>    <tr><td> 33   </td><td> 1  </td><td> file2.xml     </td></tr>    </table><table border=1><tr><th> volume   </th><th> edition  </th><th> file     </th></tr><tr><td> 55   </td><td> 3  </td><td> file1.xml     </td></tr>    <tr><td> 55   </td><td> 2  </td><td> file11.xml     </td></tr>    <tr><td> 33   </td><td> 3  </td><td> file12.xml     </td></tr>    <tr><td> 33   </td><td> 1  </td><td> file2.xml     </td></tr>    <tr><td> 55   </td><td> 1  </td><td> file21.xml     </td></tr>    <tr><td> 33   </td><td> 2  </td><td> file8.xml     </td></tr>    </table><table border=1><tr><th> volume   </th><th> edition  </th><th> file     </th></tr><tr><td> 55   </td><td> 3  </td><td> file1.xml     </td></tr>    <tr><td> 33   </td><td> 1  </td><td> file2.xml     </td></tr>    <tr><td> 33   </td><td> 2  </td><td> file8.xml     </td></tr>    <tr><td> 55   </td><td> 2  </td><td> file11.xml     </td></tr>    <tr><td> 33   </td><td> 3  </td><td> file12.xml     </td></tr>    <tr><td> 55   </td><td> 1  </td><td> file21.xml     </td></tr>    </table>' ],
  ];

?>