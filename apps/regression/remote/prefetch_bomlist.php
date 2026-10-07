<?php

  // A fetched body is data, so one that reads as a PAD list is refused rather than its
  // elements evaluated - behind a byte order mark too, which padData strips before it
  // sniffs the type and which the check did not.

  $sets = padPrefetch ( [ 'bl' => 'SELF://regression/remote/?bomlist&padInclude' ] );

?>
