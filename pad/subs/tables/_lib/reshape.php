<?php

  // Filter values are literal, case-insensitive substrings, never expressions. Only
  // named columns can read <query>filter[column] from the request. Apply before totals,
  // grouping, aggregation and pagination so every calculation describes this selection.
  function padTableFilter ( $rows, $fields, $query ) {

    $filters = $_GET [$query . 'filter'] ?? [];
    if ( ! is_array ( $filters ) ) return $rows;

    foreach ( $fields as $field ) {
      $value = $filters [$field] ?? '';
      if ( ! is_scalar ( $value ) or (string) $value === '' ) continue;
      $rows = array_values ( array_filter ( $rows, function ( $row ) use ( $field, $value ) {
        $cell = $row [$field] ?? '';
        return is_scalar ( $cell ) and mb_stripos ( (string) $cell, (string) $value ) !== FALSE;
      } ) );
    }

    return $rows;

  }

  function padTableFieldCheck ( $rows, $fields, $option ) {

    global $padCheckSyntax;
    if ( ! $rows ) return;
    $known = [];
    foreach ( $rows as $row ) $known += $row;
    foreach ( $fields as $field ) {
      if ( ! array_key_exists ( $field, $known ) and $padCheckSyntax )
        padError ( "the $option of the table names an unknown column '" . padMakeSafe ( $field, 40 ) . "'" );
      foreach ( $rows as $row )
        if ( isset ( $row [$field] ) and ! is_scalar ( $row [$field] ) and $padCheckSyntax )
          padError ( "the $option column of the table must contain scalar values" );
    }

  }

  function padTableGroupKey ( $row, $fields ) {
    return serialize ( array_map ( fn ( $field ) => $row [$field] ?? NULL, $fields ) );
  }

  function padTableGroups ( $rows, $fields, $totals ) {

    $groups = [];
    foreach ( $rows as $row ) {
      $key = padTableGroupKey ( $row, $fields );
      $groups [$key] ??= [ 'label' => implode ( ' / ', array_map ( fn ( $field ) => (string) ( $row [$field] ?? '' ), $fields ) ),
                           'rows' => [], 'sums' => array_fill_keys ( $totals, 0 ) ];
      $groups [$key] ['rows'] [] = $row;
      foreach ( $totals as $field )
        if ( is_numeric ( $row [$field] ?? NULL ) ) $groups [$key] ['sums'] [$field] += $row [$field] + 0;
    }
    return $groups;

  }

  // Use generated field names: categories may contain commas or collide with the row
  // field. The original values are retained only in escaped labels, never as options.
  function padTablePivot ( $rows, $rowField, $columnField, $valueField, $aggregate ) {

    global $padCheckSyntax;
    $columns = $records = $cells = [];
    foreach ( $rows as $row ) {
      $rowValue = $row [$rowField] ?? NULL;
      $colValue = $row [$columnField] ?? NULL;
      $rk = serialize ( $rowValue );
      $ck = serialize ( $colValue );
      $columns [$ck] ??= [ 'field' => 'value_' . count ( $columns ), 'label' => (string) $colValue ];
      $records [$rk] ??= [ 'row_label' => $rowValue ];
      $cells [$rk] [$ck] ??= [];
      if ( $aggregate === 'count' ) {
        $cells [$rk] [$ck] [] = 1;
        continue;
      }
      $value = $row [$valueField] ?? NULL;
      if ( $value === NULL or $value === '' ) continue;
      if ( is_bool ( $value ) or ! is_scalar ( $value ) or ! is_numeric ( $value ) ) {
        if ( $padCheckSyntax ) padError ( 'the value column of a pivot or matrix must contain numbers' );
        continue;
      }
      $cells [$rk] [$ck] [] = $value + 0;
    }
    foreach ( $records as $rk => &$record )
      foreach ( $columns as $ck => $column ) {
        $values = $cells [$rk] [$ck] ?? [];
        $record [ $column ['field'] ] = ! $values ? ( in_array ( $aggregate, [ 'sum', 'count' ], TRUE ) ? 0 : NULL )
          : match ( $aggregate ) {
              'count' => count ( $values ), 'avg' => array_sum ( $values ) / count ( $values ),
              'min' => min ( $values ), 'max' => max ( $values ), default => array_sum ( $values )
            };
      }
    unset ( $record );
    return [ array_values ( $records ), array_merge ( [ 'row_label' ], array_column ( $columns, 'field' ) ),
             array_merge ( [ padStrHeadline ( $rowField ) ], array_column ( $columns, 'label' ) ) ];

  }

  function padTableSubtotal ( $table, $group ) {
    $html = '<tr class="pad-table-subtotal">';
    foreach ( $table ['columns'] as $index => $column )
      if ( $column ['total'] )
        $html .= '<td class="num">' . padChartAttr ( padDatatableCell ( $group ['sums'] [$column ['field']], $column ) ) . '</td>';
      else
        $html .= $index === 0 ? '<th scope="row">' . padChartAttr ( $group ['label'] . ' subtotal' ) . '</th>' : '<td></td>';
    return $html . '</tr>';
  }

  function padTableFilterForm ( $fields, $query ) {
    if ( ! $fields ) return '';
    $href = padDatatableHref ( [], [ $query . 'page' ] );
    $html = '<form class="pad-table-filters" method="get" action="' . padChartAttr ( $href ) . '">';
    // GET forms replace the action query. Carry all existing scalar/array parameters,
    // including a bare page route, but drop engine switches and the page/filter being set.
    $hidden = function ( $name, $value ) use ( &$hidden ) {
      if ( is_array ( $value ) ) {
        $out = '';
        foreach ( $value as $key => $item ) $out .= $hidden ( $name . '[' . $key . ']', $item );
        return $out;
      }
      return '<input type="hidden" name="' . padChartAttr ( $name ) . '" value="' . padChartAttr ( $value ) . '">';
    };
    $request = [];
    parse_str ( (string) parse_url ( $href, PHP_URL_QUERY ), $request );
    foreach ( $request as $name => $value )
      if ( ! str_starts_with ( (string) $name, 'pad' ) and $name !== $query . 'page' and $name !== $query . 'filter' )
        $html .= $hidden ( $name, $value );
    $filters = $_GET [$query . 'filter'] ?? [];
    foreach ( $fields as $field ) {
      $value = is_array ( $filters ) ? ( $filters [$field] ?? '' ) : '';
      $html .= '<label>' . padChartAttr ( padStrHeadline ( $field ) ) . ' <input type="search" name="'
        . padChartAttr ( $query . 'filter[' . $field . ']' ) . '" value="' . padChartAttr ( is_scalar ( $value ) ? $value : '' ) . '"></label>';
    }
    return $html . '<button type="submit">Filter</button></form>';
  }

  function padTableExtraStyle () {
    if ( ! padWidgetOnce ( 'style:table-kinds' ) ) return '';
    return '<style' . padStyleNonce () . '>.pad-table-group th,.pad-table-subtotal{background:var(--pad-datatable-head);font-weight:600}'
      . '.pad-table-filters{display:flex;gap:.75rem;flex-wrap:wrap;align-items:end;margin-bottom:1rem}'
      . '.pad-table-filters label{display:grid;gap:.25rem}.pad-table-filters input,.pad-table-filters button{font:inherit;padding:.4rem}'
      . '.pad-table-heat-1{background:color-mix(in srgb,var(--pad-datatable-accent) 8%,transparent)}'
      . '.pad-table-heat-2{background:color-mix(in srgb,var(--pad-datatable-accent) 18%,transparent)}'
      . '.pad-table-heat-3{background:color-mix(in srgb,var(--pad-datatable-accent) 28%,transparent)}'
      . '.pad-table-heat-4{background:color-mix(in srgb,var(--pad-datatable-accent) 38%,transparent)}'
      . '.pad-table-heat-5{background:color-mix(in srgb,var(--pad-datatable-accent) 48%,transparent)}</style>';
  }

?>
