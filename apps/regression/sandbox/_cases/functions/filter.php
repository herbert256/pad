<?php

  // stripLow drops the ASCII control characters below 32; everything printable stays.

  return [

    [ 'stripLow strips the control characters out',
      <<<'PAD'
      {echo $caseLow | stripLow}
      PAD,
      'abc',
      [ 'caseLow' => "a\x01b\x02c" ] ],

  ];

?>
