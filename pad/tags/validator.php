<?php

  // {validator}: the browser's checker of padValidate's rules (lib/validate.js) as one inline
  // script, for a page whose forms a component draws - React, Alpine - and checks with
  // padValidate.check ( rules, values ), the rules from padValidateClient. A {form} with
  // client brings it along by itself; either way a page gets it once.

  return padValidateScript ();

?>
