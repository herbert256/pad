<?php

  // Reads $data as YAML and returns it as a PAD data array. Included by padData() as
  // data/<type>.php; padContentType picks 'yaml' for text opening with %YAML or ---.
  // Needs the PHP yaml extension - unlike the other readers this has no fallback.
  //
  // yaml_parse says why it failed in a warning of its own, which PAD's error handler took
  // for the error - a PHP warning from inside the engine - before the reader came to its
  // own report, which never ran. The warning is caught here and its reason goes into the
  // reader's report.

  $padYamlWhy = '';

  set_error_handler ( function ( $type, $error ) use ( &$padYamlWhy ) {
    $padYamlWhy = preg_replace ( '/^yaml_parse\(\):\s*/', '', $error );
    return TRUE;
  } );

  // Booleans as YAML 1.2 has them: true and false alone. libyaml reads YAML 1.1, where NO,
  // no, n, Off, on, y and yes are booleans too - keys among them: the ISO code of Norway was
  // false, a field named n lost its name, and a map of NO and NL drew the Netherlands only.

  $padYamlNdocs = 0;

  try {
    $result = yaml_parse ( $data, 0, $padYamlNdocs, [ 'tag:yaml.org,2002:bool' => function ( $value ) {
      return match ( strtolower ( (string) $value ) ) { 'true' => TRUE, 'false' => FALSE, default => (string) $value };
    } ] );
  } finally {
    restore_error_handler ();
  }

  if ( ! is_array ( $result ) )
    return padError ( "YAML parse error" . ( $padYamlWhy !== '' ? ": $padYamlWhy" : '' ) );

  return $result;

?>
