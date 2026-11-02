<?php

  // Metadata for the latest search: all matching hits and facets before first/paging.
  function padSearchInfo ( $value = NULL ) {
    static $info = [ 'total' => 0, 'facets' => [] ];
    if ( $value !== NULL )
      $info = $value;
    return $info;
  }

  function padSearchFilterSpec ( $filters ) {
    if ( is_string ( $filters ) ) {
      try {
        $filters = json_decode ( $filters, FALSE, 64, JSON_THROW_ON_ERROR );
      } catch ( JsonException $error ) {
        return 'filters text must be a valid JSON object';
      }
      if ( ! ( $filters instanceof stdClass ) )
        return 'filters text must be a JSON object';
      $filters = get_object_vars ( $filters );
    }
    $error = padSearchFilterError ( $filters );
    return $error === '' ? $filters : $error;
  }

  function padSearchFilterError ( $filters ) {
    if ( ! is_array ( $filters ) )
      return 'filters must be a map of field names to exact values or lists of values';
    foreach ( $filters as $field => $values ) {
      if ( ! is_string ( $field ) || ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_.-]*$/D', $field ) )
        return 'filters must use field names';
      if ( is_array ( $values ) && ! array_is_list ( $values ) )
        return 'each filter list must contain scalar values or null';
      foreach ( is_array ( $values ) ? $values : [ $values ] as $value )
        if ( ! is_scalar ( $value ) && $value !== NULL || is_float ( $value ) && ! is_finite ( $value ) )
          return 'each filter value must be a finite scalar or null';
    }
    return '';
  }

  function padSearchFacetFields ( $spec ) {
    if ( is_string ( $spec ) )
      $spec = $spec === '' ? [] : array_map ( 'trim', explode ( ',', $spec ) );
    if ( ! is_array ( $spec ) || ! array_is_list ( $spec ) )
      return 'facets must be a comma-separated string or list of field names';
    foreach ( $spec as $field )
      if ( ! is_string ( $field ) || ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_.-]*$/D', $field ) )
        return 'facets must contain field names';
    return array_values ( array_unique ( $spec ) );
  }

  // AND across fields, OR within a field's values. Scalars are compared with their types;
  // a list-valued row matches any of its items. A missing property never equals null.
  function padSearchFilterRows ( $rows, $filters ) {
    $missing = new stdClass;
    return array_values ( array_filter ( (array) $rows, function ( $source ) use ( $filters, $missing ) {
      $row = padSearchRow ( $source );
      foreach ( $filters as $field => $allowed ) {
        $value = padArrGet ( $row, $field, $missing );
        if ( $value === $missing ) return FALSE;
        $allowed = is_array ( $allowed ) ? $allowed : [ $allowed ];
        $matched = FALSE;
        foreach ( is_array ( $value ) ? $value : [ $value ] as $item )
          if ( in_array ( $item, $allowed, TRUE ) ) { $matched = TRUE; break; }
        if ( ! $matched ) return FALSE;
      }
      return TRUE;
    } ) );
  }

  // Each value is counted once per matching document, including list-valued fields.
  // Typed values stay distinct; sorted buckets keep results deterministic on score ties.
  function padSearchFacetCounts ( $rows, $fields ) {
    $counts = array_fill_keys ( $fields, [] );
    $missing = new stdClass;
    foreach ( $rows as $source ) {
      $row = padSearchRow ( $source );
      foreach ( $fields as $field ) {
        $value = padArrGet ( $row, $field, $missing );
        if ( $value === $missing ) continue;
        $seen = [];
        foreach ( is_array ( $value ) ? $value : [ $value ] as $item ) {
          if ( ! is_scalar ( $item ) && $item !== NULL || is_float ( $item ) && ! is_finite ( $item ) ) continue;
          $key = get_debug_type ( $item ) . ':' . json_encode ( $item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
          if ( isset ( $seen [$key] ) ) continue;
          $seen [$key] = TRUE;
          $counts [$field] [$key] ??= [ 'value' => $item, 'count' => 0 ];
          $counts [$field] [$key] ['count']++;
        }
      }
    }
    foreach ( $counts as $field => $buckets ) {
      $buckets = array_values ( $buckets );
      usort ( $buckets, fn ( $a, $b ) => strcmp ( (string) $a ['value'], (string) $b ['value'] ) ?: strcmp ( get_debug_type ( $a ['value'] ), get_debug_type ( $b ['value'] ) ) );
      $counts [$field] = $buckets;
    }
    return $counts;
  }

?>
