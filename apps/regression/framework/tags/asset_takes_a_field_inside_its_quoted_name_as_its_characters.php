<?php

  // A field written inside the quoted name of {pad:asset} - and of {image:thumbnail} and {widget:video}, which
  // check their file the same way - is the name it holds: the @ of logo@2x.css came as its
  // stand-in, and the name was refused as no file name below www/<application>/. The
  // fixture is www/regression/framework/assets/logo@2x.css.

  $f = 'logo@2x.css';

?>
