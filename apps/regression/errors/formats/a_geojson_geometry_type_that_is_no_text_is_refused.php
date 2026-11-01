<?php

  // A geometry's type is a text: a list there is the document's error, where it was cast
  // to text - "Array to string conversion".

  $ini = padFormatRead ( 'geojson', "{\"type\":\"Feature\",\n\"geometry\":{\"type\":[\"x\"]},\"properties\":{}}" );

?>
