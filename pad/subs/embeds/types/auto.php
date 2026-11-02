<?php

  // auto - Detect the provider from an address: {embed:auto 'https://youtu.be/aqz-KE-bpKQ', title='A video'/}.
  // The embed dispatcher resolves this selector before calling the provider. It does not
  // claim addresses itself, so provider discovery and CSP enumeration remain finite.

  return [ 'name' => 'Automatic provider', 'kind' => 'video', 'frame' => '', 'options' => [],
           'generic' => TRUE, 'match' => fn ( $source, $named ) => NULL,
           'embed' => fn ( $parts, $opts ) => [] ];

?>
