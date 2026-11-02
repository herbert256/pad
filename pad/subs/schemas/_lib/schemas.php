<?php
  // Extracted from the AI boundary. These compatibility routines preserve its first
  // failure, scalar enum identity, stdClass distinction and exact error strings.
  function padSchemaIssue ( $path, $code, $message ) { return compact ( 'path', 'code', 'message' ); }

  function padSchemaDefinitionError ( $schema, $path = 'schema', $depth = 0 ) {
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
        if ( ( $error = padSchemaDefinitionError ( $child, "$path.properties.$key", $depth + 1 ) ) !== '' )
          return $error;
      }
    }
    if ( array_key_exists ( 'items', $schema ) && ( $error = padSchemaDefinitionError ( $schema ['items'], "$path.items", $depth + 1 ) ) !== '' )
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

  function padSchemaValueIssue ( $value, $schema, $path = '$', $depth = 0 ) {
    if ( $depth > 32 )
      return padSchemaIssue ( $path, 'depth', "$path exceeds the validation depth limit" );
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
      return padSchemaIssue ( $path, 'type', "$path must be $type" );
    if ( isset ( $schema ['enum'] ) && ! in_array ( $value, $schema ['enum'], TRUE ) )
      return padSchemaIssue ( $path, 'enum', "$path is outside its enum" );
    if ( is_int ( $value ) || is_float ( $value ) ) {
      if ( ! is_finite ( (float) $value ) ) return padSchemaIssue ( $path, 'finite', "$path must be finite" );
      if ( isset ( $schema ['minimum'] ) && $value < $schema ['minimum'] ) return padSchemaIssue ( $path, 'minimum', "$path is below minimum" );
      if ( isset ( $schema ['maximum'] ) && $value > $schema ['maximum'] ) return padSchemaIssue ( $path, 'maximum', "$path is above maximum" );
    }
    if ( is_string ( $value ) ) {
      $length = mb_strlen ( $value, 'UTF-8' );
      if ( isset ( $schema ['minLength'] ) && $length < $schema ['minLength'] ) return padSchemaIssue ( $path, 'minLength', "$path is shorter than minLength" );
      if ( isset ( $schema ['maxLength'] ) && $length > $schema ['maxLength'] ) return padSchemaIssue ( $path, 'maxLength', "$path is longer than maxLength" );
    }
    if ( is_array ( $value ) ) {
      if ( isset ( $schema ['minItems'] ) && count ( $value ) < $schema ['minItems'] ) return padSchemaIssue ( $path, 'minItems', "$path has fewer than minItems" );
      if ( isset ( $schema ['maxItems'] ) && count ( $value ) > $schema ['maxItems'] ) return padSchemaIssue ( $path, 'maxItems', "$path has more than maxItems" );
      foreach ( isset ( $schema ['items'] ) ? $value : [] as $key => $child )
        if ( ( $error = padSchemaValueIssue ( $child, $schema ['items'], $path . '[' . $key . ']', $depth + 1 ) ) !== NULL ) return $error;
    }
    if ( $value instanceof stdClass ) {
      foreach ( $schema ['required'] ?? [] as $key )
        if ( ! property_exists ( $value, $key ) ) return padSchemaIssue ( "$path.$key", 'required', "$path.$key is required" );
      foreach ( get_object_vars ( $value ) as $key => $child ) {
        if ( array_key_exists ( $key, $schema ['properties'] ?? [] ) ) {
          if ( ( $error = padSchemaValueIssue ( $child, $schema ['properties'] [$key], "$path.$key", $depth + 1 ) ) !== NULL ) return $error;
        } elseif ( ( $schema ['additionalProperties'] ?? TRUE ) === FALSE ) return padSchemaIssue ( "$path.$key", 'additionalProperties', "$path.$key is an additional property" );
      }
    }
    return NULL;
  }


  function padSchemaValueError ( $value, $schema, $path = '$', $depth = 0 ) {
    return padSchemaValueIssue ( $value, $schema, $path, $depth ) ['message'] ?? '';
  }

  function padSchemaBound ( $value, &$remaining, $path = '$', $depth = 0, $data = TRUE ) {
    if ( --$remaining < 0 ) return padSchemaIssue ( $path, 'limit', 'validation exceeds the 100000-value budget' );
    if ( $depth > 64 ) return padSchemaIssue ( $path, 'depth', 'input exceeds the 64-level structural bound' );
    if ( $data && is_object ( $value ) && ! $value instanceof stdClass ) return padSchemaIssue ( $path, 'type', 'only stdClass objects are supported' );
    if ( $data && is_array ( $value ) && ! array_is_list ( $value ) ) return padSchemaIssue ( $path, 'type', 'JSON arrays must be lists; use stdClass for objects' );
    if ( $data && is_resource ( $value ) ) return padSchemaIssue ( $path, 'type', 'resources are not JSON values' );
    if ( $data && is_string ( $value ) && ! mb_check_encoding ( $value, 'UTF-8' ) ) return padSchemaIssue ( $path, 'encoding', 'strings must be valid UTF-8' );
    if ( $data && is_float ( $value ) && ! is_finite ( $value ) ) return padSchemaIssue ( $path, 'finite', "$path must be finite" );
    foreach ( is_array ( $value ) || $value instanceof stdClass ? $value : [] as $key => $child ) {
      if ( $data && $value instanceof stdClass && is_string ( $key ) && ! mb_check_encoding ( $key, 'UTF-8' ) )
        return padSchemaIssue ( $path, 'encoding', 'object property names must be valid UTF-8' );
      $childPath = is_array ( $value ) ? $path . '[' . $key . ']' : "$path.$key";
      $issue = padSchemaBound ( $child, $remaining, $childPath, $depth + 1, $data );
      if ( $issue !== NULL ) return $issue;
    }
    return NULL;
  }

  function padSchemaRun ( $kind, $value, $schema ) {
    $remaining = 100000; $issue = padSchemaBound ( $schema, $remaining, 'schema', 0, FALSE );
    if ( $issue === NULL ) {
      $error = padSchemaDefinitionError ( $schema );
      if ( $error !== '' ) $issue = padSchemaIssue ( 'schema', 'schema', $error );
    }
    if ( $issue !== NULL ) return [ 'valid' => FALSE, 'issues' => [ $issue ], 'kind' => $kind ];
    if ( $kind !== 'jsonschema' ) {
      if ( isset ( $schema ['type'] ) && $schema ['type'] !== 'object' )
        return [ 'valid' => FALSE, 'issues' => [ padSchemaIssue ( 'schema.type', 'schema', 'record and rowset require an object schema' ) ], 'kind' => $kind ];
      $schema ['type'] = 'object';
      if ( $kind === 'rowset' ) {
        if ( ! is_array ( $value ) || ! array_is_list ( $value ) || count ( $value ) > 10000 )
          return [ 'valid' => FALSE, 'issues' => [ padSchemaIssue ( '$', 'type', 'rowset requires a list of at most 10000 records' ) ], 'kind' => $kind ];
        $value = array_map ( fn ( $row ) => is_array ( $row ) && ( ! array_is_list ( $row ) || $row === [] ) ? (object) $row : $row, $value );
        $schema = [ 'type' => 'array', 'items' => $schema ];
      } elseif ( is_array ( $value ) && ( ! array_is_list ( $value ) || $value === [] ) ) $value = (object) $value;
    }
    $remaining = 100000; $issue = padSchemaBound ( $value, $remaining );
    $issue ??= padSchemaValueIssue ( $value, $schema );
    return [ 'valid' => $issue === NULL, 'issues' => $issue === NULL ? [] : [ $issue ], 'kind' => $kind ];
  }
?>
