<?php

  // A bounded JSON Schema subset, applied locally to provider text. No claim is made
  // that a provider enforces the schema. Unknown keywords are rejected, never ignored.
  function padAiJsonSchemaError ( $schema, $path = 'schema', $depth = 0 ) {
    include_once PAD . 'subs/schemas/_lib/schemas.php';
    return padSchemaDefinitionError ( $schema, $path, $depth );
  }

  function padAiJsonValueError ( $value, $schema, $path = '$', $depth = 0 ) {
    include_once PAD . 'subs/schemas/_lib/schemas.php';
    return padSchemaValueError ( $value, $schema, $path, $depth );
  }

  // Returns an error string, leaving the successfully decoded value in padAiStore('json').
  // Objects remain stdClass so {} and [] stay distinct. Fences and surrounding prose fail.
  function padAiJsonFinite ( $value ) {
    if ( is_float ( $value ) && ! is_finite ( $value ) ) return FALSE;
    foreach ( is_array ( $value ) || $value instanceof stdClass ? $value : [] as $child )
      if ( ! padAiJsonFinite ( $child ) ) return FALSE;
    return TRUE;
  }

  function padAiJsonAnswer ( $text, $schema ) {
    padAiStore ( 'json', NULL );
    try {
      $value = json_decode ( $text, FALSE, 64, JSON_THROW_ON_ERROR );
    } catch ( JsonException $error ) {
      return 'the answer is not valid JSON';
    }
    if ( ! padAiJsonFinite ( $value ) )
      return 'the answer contains a number outside the supported finite range';
    $error = padAiJsonValueError ( $value, $schema );
    if ( $error === '' )
      padAiStore ( 'json', $value );
    return $error;
  }

?>
