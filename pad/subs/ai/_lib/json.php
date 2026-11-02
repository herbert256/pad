<?php

  // A bounded JSON Schema subset, applied locally to provider text. No claim is made
  // that a provider enforces the schema. Unknown keywords are rejected, never ignored.
  function padAiJsonSchemaError ( $schema, $path = 'schema', $depth = 0 ) {
    if ( $depth > 32 || ! is_array ( $schema ) )
      return "$path must be a schema array, nested at most 32 levels";
    $keys = [ 'type', 'properties', 'required', 'additionalProperties', 'items', 'enum', 'minimum', 'maximum', 'minLength', 'maxLength', 'minItems', 'maxItems' ];
    foreach ( $schema as $key => $value )
      if ( ! in_array ( $key, $keys, TRUE ) )
        return "$path has an unsupported keyword " . (string) $key;
    if ( isset ( $schema ['type'] ) && ! in_array ( $schema ['type'], [ 'object', 'array', 'string', 'integer', 'number', 'boolean', 'null' ], TRUE ) )
      return "$path.type is object, array, string, integer, number, boolean or null";
    if ( array_key_exists ( 'type', $schema ) && $schema ['type'] === NULL )
      return "$path.type must be a type name";
    if ( array_key_exists ( 'properties', $schema ) ) {
      if ( ! is_array ( $schema ['properties'] ) )
        return "$path.properties must be a map of property schemas";
      foreach ( $schema ['properties'] as $key => $child ) {
        if ( ! is_string ( $key ) && ! is_int ( $key ) )
          return "$path.properties must use property names";
        if ( ( $error = padAiJsonSchemaError ( $child, "$path.properties.$key", $depth + 1 ) ) !== '' )
          return $error;
      }
    }
    if ( array_key_exists ( 'items', $schema ) && ( $error = padAiJsonSchemaError ( $schema ['items'], "$path.items", $depth + 1 ) ) !== '' )
      return $error;
    if ( array_key_exists ( 'required', $schema ) && ( ! is_array ( $schema ['required'] ) || ! array_is_list ( $schema ['required'] ) || array_filter ( $schema ['required'], fn ( $v ) => ! is_string ( $v ) ) ) )
      return "$path.required must be a list of property names";
    if ( array_key_exists ( 'additionalProperties', $schema ) && ! is_bool ( $schema ['additionalProperties'] ) )
      return "$path.additionalProperties must be true or false";
    if ( array_key_exists ( 'enum', $schema ) && ( ! is_array ( $schema ['enum'] ) || ! array_is_list ( $schema ['enum'] ) || ! $schema ['enum'] || array_filter ( $schema ['enum'], fn ( $v ) => ! is_scalar ( $v ) && $v !== NULL ) ) )
      return "$path.enum must be a nonempty list of scalar values or null";
    foreach ( [ 'minimum', 'maximum' ] as $key )
      if ( array_key_exists ( $key, $schema ) && ( ! is_int ( $schema [$key] ) && ! is_float ( $schema [$key] ) || ! is_finite ( (float) $schema [$key] ) ) )
        return "$path.$key must be a finite number";
    foreach ( [ 'minLength', 'maxLength', 'minItems', 'maxItems' ] as $key )
      if ( array_key_exists ( $key, $schema ) && ( ! is_int ( $schema [$key] ) || $schema [$key] < 0 ) )
        return "$path.$key must be a nonnegative integer";
    return '';
  }

  function padAiJsonValueError ( $value, $schema, $path = '$', $depth = 0 ) {
    if ( $depth > 32 )
      return "$path exceeds the validation depth limit";
    $type = $schema ['type'] ?? '';
    $valid = match ( $type ) {
      'object' => $value instanceof stdClass,
      'array' => is_array ( $value ),
      'string' => is_string ( $value ),
      'integer' => is_int ( $value ) || ( is_float ( $value ) && is_finite ( $value ) && floor ( $value ) === $value ),
      'number' => is_int ( $value ) || ( is_float ( $value ) && is_finite ( $value ) ),
      'boolean' => is_bool ( $value ),
      'null' => $value === NULL,
      default => TRUE
    };
    if ( ! $valid )
      return "$path must be $type";
    if ( isset ( $schema ['enum'] ) && ! in_array ( $value, $schema ['enum'], TRUE ) )
      return "$path is outside its enum";
    if ( is_int ( $value ) || is_float ( $value ) ) {
      if ( ! is_finite ( (float) $value ) ) return "$path must be finite";
      if ( isset ( $schema ['minimum'] ) && $value < $schema ['minimum'] ) return "$path is below minimum";
      if ( isset ( $schema ['maximum'] ) && $value > $schema ['maximum'] ) return "$path is above maximum";
    }
    if ( is_string ( $value ) ) {
      $length = mb_strlen ( $value, 'UTF-8' );
      if ( isset ( $schema ['minLength'] ) && $length < $schema ['minLength'] ) return "$path is shorter than minLength";
      if ( isset ( $schema ['maxLength'] ) && $length > $schema ['maxLength'] ) return "$path is longer than maxLength";
    }
    if ( is_array ( $value ) ) {
      if ( isset ( $schema ['minItems'] ) && count ( $value ) < $schema ['minItems'] ) return "$path has fewer than minItems";
      if ( isset ( $schema ['maxItems'] ) && count ( $value ) > $schema ['maxItems'] ) return "$path has more than maxItems";
      foreach ( isset ( $schema ['items'] ) ? $value : [] as $key => $child )
        if ( ( $error = padAiJsonValueError ( $child, $schema ['items'], $path . '[' . $key . ']', $depth + 1 ) ) !== '' ) return $error;
    }
    if ( $value instanceof stdClass ) {
      foreach ( $schema ['required'] ?? [] as $key )
        if ( ! property_exists ( $value, $key ) ) return "$path.$key is required";
      foreach ( get_object_vars ( $value ) as $key => $child ) {
        if ( array_key_exists ( $key, $schema ['properties'] ?? [] ) ) {
          if ( ( $error = padAiJsonValueError ( $child, $schema ['properties'] [$key], "$path.$key", $depth + 1 ) ) !== '' ) return $error;
        } elseif ( ( $schema ['additionalProperties'] ?? TRUE ) === FALSE ) return "$path.$key is an additional property";
      }
    }
    return '';
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
