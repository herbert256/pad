<?php

  // The tidy option runs the finished text of its level through padTidy(), the same
  // normalisation the {tidy} tag applies to what it wraps.
  //
  // Two more options in pad/options/ have no case here, each for its own reason: dump=
  // writes a full state dump under DATA, a side effect a suite must not repeat on every
  // run - the {dump} tag has a pages test instead - and error= is documented as an alias
  // of notOk= whose handler nothing includes, so it is not actually wired up.

  return [

    [ 'tidy normalises what the level produced',
      <<<'PAD'
      {true tidy}<i >y</i >{/true}
      PAD,
      '<i>y</i>' ],

  ];

?>
