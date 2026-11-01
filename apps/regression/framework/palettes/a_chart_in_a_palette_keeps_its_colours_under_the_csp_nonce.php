<?php

  // Under the style-src nonce the engine supports a style attribute is dropped - a nonce
  // covers no attribute - so the colours of a palette are a rule of the chart's class in its
  // own <style>, which carries the nonce: they stood in a style attribute of the svg, and
  // under the policy a viridis chart was drawn in the default blue.

  $padCsp = "script-src 'nonce'; style-src 'nonce'";

?>
