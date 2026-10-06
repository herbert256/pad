<?php

  // padUpload's max= in bytes: a size beyond what a number holds - 99999999999G - is the
  // largest there is, no limit in practice. Its cast to a whole number was reported by
  // PHP 8.5 as a float it cannot represent, which ended the request, and came out negative:
  // every file was larger than it.

  $sizes = json_encode ( [ padUploadBytes ( '500K' ), padUploadBytes ( '1.5G' ), padUploadBytes ( '2MB' ),
                           padUploadBytes ( '99999999999G' ) === PHP_INT_MAX ] );

?>
