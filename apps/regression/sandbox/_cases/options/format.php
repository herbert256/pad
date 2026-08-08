<?php

  // The tidy option runs the finished text of its level through padTidy(), the same
  // normalisation the {tidy} tag applies to what it wraps.
  //
  // dump= has no case: it writes a full state dump under DATA, a side effect a suite must
  // not repeat on every run - the {dump} tag is a pages test instead. The other options
  // have their cases in flow.php and options.php.

  return [

    [ 'tidy normalises what the level produced',
      <<<'PAD'
      {true tidy}<i >y</i >{/true}
      PAD,
      '<i>y</i>' ],

  ];

?>
