<?php

  // The providers group: look the path $names up in the data a {reactData} provider
  // returned, so a template can reach the same records it handed to React.

  global $padProviders;

  return padAtSearch ( $padProviders [$padIdx], $names );

?>