<?php

  // Loop control as the check application exercises it: break, cease and continue over the
  // shared staff data.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.
  //
  // Where check wrapped its examples in {table}{demo} to show source beside result, that
  // scaffolding is stripped: it is presentation from the _common application, not behaviour
  // under test, and the demos of one page share their data so they stay one case.

  return [

    [ 'control/break',
      <<<'PAD'
      <table border=1>
        {staff}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {break}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {break 'staff'}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {break -2}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      PAD,
      '<table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr></table>' ],

    [ 'control/cease',
      <<<'PAD'
      <table border=1>
        {staff}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {cease}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {cease 'staff'}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {cease -2}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      PAD,
      '<table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr></table>' ],

    [ 'control/continue',
      <<<'PAD'
      <table border=1>
        {staff}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {continue}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {continue 'staff'}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      
      <hr>
      
      <table border=1>
        {staff}
          {if $name eq 'jack'}
            {continue -2}
          {/if}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      PAD,
      '<table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table><hr><table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table>' ],

    [ 'control/index',
      <<<'PAD'
      {staff}
      
        {$name} <br>
      
      {/staff}
      
      {staff}
      
        {if $name eq 'jack'}
          {continue 'staff'}
        {/if}
      
        {$name} <br>
      
      {/staff}
      
      {staff}
      
        {if $name eq 'jack'}
          {cease 'staff'}
        {/if}
      
        {$name} <br>
      
      {/staff}
      
      {staff}
      
        {if $name eq 'jack'}
          {break 'staff'}
        {/if}
      
        {$name} <br>
      
      {/staff}
      PAD,
      'joe <br>jim <br>john <br>jack <br>jerry <br>joe <br>jim <br>john <br>jerry <br>joe <br>jim <br>john <br>jack <br>joe <br>jim <br>john <br>' ],
  ];

?>