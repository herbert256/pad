<?php

  // Reads $data as YAML and returns it as a PAD data array. Included by padData() as
  // data/<type>.php; padContentType picks 'yaml' for text opening with %YAML or ---.
  // Needs the PHP yaml extension - unlike the other readers this has no fallback.

  $result = yaml_parse ($data);

  if ( ! is_array($result) or $result === NULL or $result === FALSE)
    return padError ( "YAML parse error" );

  return $result;

?>