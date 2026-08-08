<?php

  // Two prefixes the other files leave out: common: names the shared application outright,
  // and parm: reads a parameter or option of the nearest tag that carries one.

  return [

    [ 'common: reaches the shared application by name',
      <<<'PAD'
      {common:block}x{/common:block}
      PAD,
      '/<table[^>]*>.*<td>x<\/td>.*<\/table>/' ],

    [ 'parm: reads an option of the nearest tag',
      <<<'PAD'
      {true label='v'}{parm:label}{/true}
      PAD,
      'v' ],

  ];

?>
