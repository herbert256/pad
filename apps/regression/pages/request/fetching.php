<?php

  // The fixture for request/an_exported_page_fetches_this_site_as_this_machine: the page
  // fetches a page of its own site, which answers who it took the fetch for.

  $fetched = trim ( strip_tags ( padCurl ( $padHost . 'regression/pages/?request/standing&padInclude' ) ['data'] ?? '' ) );

?>
