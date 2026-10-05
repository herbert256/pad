<?php

  // Two slow sources asked for at once: each lands in the data store under its name, and
  // the two were served at the same time - one after the other, the second would have
  // started only when the first had ended. This suite runs one page at a time, so the
  // server has the workers to serve both together.

  $sets = padPrefetch ( [
    'one' => 'SELF://regression/remote/?slow&padInclude&n=1',
    'two' => 'SELF://regression/remote/?slow&padInclude&n=2',
  ] );

  $first  = reset ( $sets ['one'] );
  $second = reset ( $sets ['two'] );

  $together = ( max ( $first ['start'], $second ['start'] ) < min ( $first ['end'], $second ['end'] ) )
            ? 'together' : 'one after the other';

?>
