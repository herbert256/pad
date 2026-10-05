<?php

  // The same source prefetched twice with a ttl: the second time it is answered from the
  // copy the first one kept.

  padPrefetch ( [ 'kept'  => 'SELF://regression/remote/?stamp&padInclude&as=prefetch' ], 60 );
  padPrefetch ( [ 'again' => 'SELF://regression/remote/?stamp&padInclude&as=prefetch' ], 60 );

?>
