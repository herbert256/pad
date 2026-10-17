<?php

  // A tag's answer spliced into the expression of the eval sequence - {echo $q} inside its
  // quotes - is refused as a field's value is: it went in bare and ran, 3 3.

  $q = '0 + php:abs(-3)';

?>
