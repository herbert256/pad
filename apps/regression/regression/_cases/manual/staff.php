<?php

  // The staff examples the manual opens with, over the shared _common data.
  //
  // These are the examples the manual shows, not its prose pages: each is a file the manual
  // embeds with {example}, carried over with the answer it is supposed to give. The manual
  // application is not changed - an example lives in both places until you decide otherwise.

  return [

    [ 'staff/staff1',
      <<<'PAD'
      <xml>
        <row name="joe"   salary="1000" bonus="500" phone="555-1111" />
        <row name="jim"   salary="2000" bonus="400" phone="555-2222" />
        <row name="john"  salary="3000" bonus="300" phone="555-3333" />
        <row name="jack"  salary="4000" bonus="200" phone="555-4444" />
        <row name="jerry" salary="5000" bonus="100" phone="555-5555" />
      </xml>
      PAD,
      '<xml><row name="joe"   salary="1000" bonus="500" phone="555-1111" /><row name="jim"   salary="2000" bonus="400" phone="555-2222" /><row name="john"  salary="3000" bonus="300" phone="555-3333" /><row name="jack"  salary="4000" bonus="200" phone="555-4444" /><row name="jerry" salary="5000" bonus="100" phone="555-5555" /></xml>' ],

    [ 'staff/staff2',
      <<<'PAD'
      <table border=1>
        {staff}
          <tr>
            <td> {$name} </td>
            <td> {$phone} </td>
          </tr>
        {/staff}
      </table>
      PAD,
      '<table border=1><tr><td> joe </td><td> 555-1111 </td></tr><tr><td> jim </td><td> 555-2222 </td></tr><tr><td> john </td><td> 555-3333 </td></tr><tr><td> jack </td><td> 555-4444 </td></tr><tr><td> jerry </td><td> 555-5555 </td></tr></table>' ],

  ];

?>