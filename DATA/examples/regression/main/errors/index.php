<?php

  // Overview of the Errors suite - the tests that fail on purpose, every case an expected
  // HTTP 500 answered under the boot action: lean, deterministic, and no dump on disk.
  // Test here reruns this suite; a page load reads the last run.

  getSuitePage ( 'errors' );

?>