<?php

  // The img tag decodes a source once for the thumbnails, the dots and the placeholders of one
  // picture, and lets it go when that picture is done - a page of two large pictures needs the
  // memory of one. The source is decoded here first, by the function the tag decodes with, so
  // the case does not depend on whether the thumbnails below are made in this request or were
  // made before: after the {pad:img}, nothing is kept.

  include_once PAD . 'tags/_lib/img.php';

  padImgDraw ( dirname ( APPS ) . '/www/regression/framework/assets/photo.jpg', 'jpeg', 1, [ 0, 0, 320, 213 ], 32, 21, TRUE );

?>
