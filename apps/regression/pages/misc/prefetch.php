<?php

  // Two slow sources asked for at once: each lands in the data store under its name. That
  // the two were on the wire together is not asserted here - whether they overlap depends
  // on the server having two idle workers while the suite runs its pages side by side, so
  // a timing answer failed under load with nothing wrong in the engine. The speed-up is
  // measured in the manual's page on parallel fetching instead.

  padPrefetch ( [
    'one' => 'SELF://regression/pages/?misc/slow&padInclude&n=1',
    'two' => 'SELF://regression/pages/?misc/slow&padInclude&n=2',
  ] );

?>
