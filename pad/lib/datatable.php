<?php

  // Tables from rows - the {datatable} tag. A complete HTML table is written on the server:
  // a header from the field names, numbers right-aligned and formatted alike down their
  // column, a totals row, header links that sort and page links that page - each a plain
  // link to the same page, so it needs no JavaScript and works in print and in an e-mail.
  //
  //   {datatable data='orders', columns='number, customer, total', totals='total'}
  //   {datatable data='orders', sortable, rows=10, striped, caption='Orders'}
  //   {datatable totals='amount'} month,amount ... {/datatable}
  //
  // padDatatableColumns  the columns: those columns= names, else every scalar field of the
  //                      first row
  // padDatatableNumeric  whether a column is one of numbers: every filled value numeric
  // padDatatableDecimals the decimals a number column shows: the most any of its values has
  // padDatatableSort     the rows in the order the request asks - sort= names a column the
  //                      table shows, dir= asc or desc - stable, so equal values keep their
  //                      order; a value that names no column leaves the order as it was
  // padDatatableCell     one value as its cell shows it: through the column's format= pipe,
  //                      else a number with the column's decimals, grouped by thousands
  //                      unless the column is an identifier (id, number, code, year ...)
  // padDatatableHref     a link to this page with request values set and others left out
  // padDatatableHtml     the table, the totals row and the page links
  // padDatatableStyle    the style - once per page
  //
  // Accessible: a <caption>, th scope="col" and, on the sorted column, aria-sort; the page
  // links are a nav with the current page marked aria-current="page". The colours are CSS
  // custom properties with light and dark defaults on .pad-datatable - --pad-datatable-text,
  // -line, -head, -stripe, -accent and -muted - which a page overrides.

  function padDatatableColumns ( $rows, $list ) {

    $fields = padChartFields ( $list );

    if ( $fields )
      return $fields;

    foreach ( $rows as $row ) {
      foreach ( padChartRow ( $row ) as $key => $value )
        if ( is_scalar ( $value ) or $value === NULL )
          $fields [] = (string) $key;
      break;
    }

    return $fields;

  }

  function padDatatableNumeric ( $rows, $field ) {

    $seen = FALSE;

    foreach ( $rows as $row ) {

      $value = $row [$field] ?? '';

      if ( $value === '' or $value === NULL )
        continue;

      if ( is_bool ( $value ) or ! is_scalar ( $value ) or ! is_numeric ( trim ( (string) $value ) ) )
        return FALSE;

      $seen = TRUE;

    }

    return $seen;

  }

  function padDatatableDecimals ( $rows, $field ) {

    $decimals = 0;

    foreach ( $rows as $row ) {

      $value = trim ( (string) ( $row [$field] ?? '' ) );

      if ( is_numeric ( $value ) and preg_match ( '/\.(\d+)$/', $value, $match ) )
        $decimals = max ( $decimals, strlen ( $match [1] ) );

    }

    return min ( 4, $decimals );

  }

  // A column whose name says it identifies - id, order_number, zipCode, year - shows its
  // numbers as they are: 10,100 is no order number.

  function padDatatableIdentifier ( $field ) {

    return (bool) preg_match ( '/(^|_|[a-z])(id|ids|no|nr|num|number|code|year|zip|postcode|phone|ean|isbn)$/i', $field );

  }

  function padDatatableSort ( $rows, $field, $desc ) {

    usort ( $rows, function ( $a, $b ) use ( $field, $desc ) {

      $x = $a [$field] ?? '';
      $y = $b [$field] ?? '';

      if ( is_scalar ( $x ) and is_scalar ( $y ) and is_numeric ( trim ( (string) $x ) ) and is_numeric ( trim ( (string) $y ) ) )
        $order = ( trim ( (string) $x ) + 0 ) <=> ( trim ( (string) $y ) + 0 );
      else
        $order = strnatcasecmp ( is_scalar ( $x ) ? (string) $x : '', is_scalar ( $y ) ? (string) $y : '' );

      return $desc ? -$order : $order;

    } );

    return $rows;

  }

  // A format is a pipe written after the column's name - total:money, created:date('j M Y')
  // - and run as {echo $value | money} would run it.

  function padDatatableCell ( $value, $column ) {

    if ( $value === NULL or ( ! is_scalar ( $value ) ) )
      $value = '';

    if ( $column ['format'] !== '' )
      return (string) padEval ( '@ | ' . $column ['format'], $value );

    if ( is_bool ( $value ) )
      return $value ? 'yes' : 'no';

    if ( $column ['numeric'] and is_numeric ( trim ( (string) $value ) ) )
      return number_format ( trim ( (string) $value ) + 0, $column ['decimals'], '.', $column ['plain'] ? '' : ',' );

    return (string) $value;

  }

  // A link to the page the request asked for, as the pager writes one: $padGo, the page, the
  // request's other values - not the engine's pad* switches - and these values set; the
  // names in $drop are left out, as a sort sends a table back to its first page.

  function padDatatableHref ( $set, $drop = [] ) {

    global $padGo, $padStartPage;

    $keep  = [];
    $first = array_key_first ( $_GET );

    foreach ( $_GET as $key => $value )
      if     ( $key === $first and $value === '' ) continue;
      elseif ( array_key_exists ( (string) $key, $set ) or in_array ( (string) $key, $drop, TRUE ) ) continue;
      elseif ( str_starts_with ( (string) $key, 'pad' ) ) continue;
      else   $keep [$key] = $value;

    return $padGo . $padStartPage . '&' . http_build_query ( $keep + $set );

  }

  // $table: the columns - field, label, numeric, decimals, plain, format, total - the rows of
  // this page, the sums, and the switches: caption, striped, sortable with the sort field,
  // its direction and the request names, and the pager's items.

  function padDatatableHtml ( $table ) {

    $catalog = padTransCatalog ();
    $html    = padDatatableStyle () . '<div class="pad-datatable">'
             . '<table class="pad-datatable-table' . ( $table ['striped'] ? ' pad-datatable-striped' : '' ) . '">';

    if ( $table ['caption'] !== '' )
      $html .= '<caption>' . padChartAttr ( $table ['caption'] ) . '</caption>';

    $html .= '<thead><tr>';

    foreach ( $table ['columns'] as $column ) {

      $class = $column ['numeric'] ? ' class="num"' : '';
      $label = padChartAttr ( $column ['label'] );
      $sort  = '';

      if ( $table ['sortable'] ) {

        $current = ( $column ['field'] === $table ['sort'] );

        if ( $current )
          $sort = ' aria-sort="' . ( $table ['desc'] ? 'descending' : 'ascending' ) . '"';

        $href  = padDatatableHref ( [ $table ['sortKey'] => $column ['field'],
                                      $table ['dirKey']  => ( $current and ! $table ['desc'] ) ? 'desc' : 'asc' ],
                                    [ $table ['pageKey'] ] );
        $arrow = $current ? ( $table ['desc'] ? '▼' : '▲' ) : '↕';
        $label = '<a href="' . padChartAttr ( $href ) . '">' . $label
               . '<span class="pad-datatable-arrow" aria-hidden="true">' . $arrow . '</span></a>';

      }

      $html .= "<th scope=\"col\"$class$sort>$label</th>";

    }

    $html .= '</tr></thead><tbody>';

    foreach ( $table ['rows'] as $row ) {

      $html .= '<tr>';

      foreach ( $table ['columns'] as $column )
        $html .= '<td' . ( $column ['numeric'] ? ' class="num"' : '' ) . '>'
               . padChartAttr ( padDatatableCell ( $row [ $column ['field'] ] ?? '', $column ) ) . '</td>';

      $html .= '</tr>';

    }

    $html .= '</tbody>';

    // The totals row: the sum under each totals= column, the word Total in the first cell
    // when that column has no sum of its own.

    if ( $table ['totals'] ) {

      $html .= '<tfoot><tr>';

      foreach ( $table ['columns'] as $index => $column )
        if ( $column ['total'] )
          $html .= '<td class="num">' . padChartAttr ( padDatatableCell ( $table ['sums'] [ $column ['field'] ], $column ) ) . '</td>';
        elseif ( $index == 0 )
          $html .= '<th scope="row">' . padChartAttr ( $catalog ['datatable.total'] ?? 'Total' ) . '</th>';
        else
          $html .= '<td></td>';

      $html .= '</tr></tfoot>';

    }

    $html .= '</table>';

    if ( $table ['pager'] )
      $html .= padDatatablePager ( $table ['pager'], $catalog );

    return $html . '</div>';

  }

  // The page links, from the rows padPagerItems makes for the {pager} tag - its own markup
  // and class, so a page's .pager style is not the table's.

  function padDatatablePager ( $items, $catalog ) {

    $out = [];

    foreach ( $items as $item ) {

      $href  = padChartAttr ( $item ['href'] );
      $label = padChartAttr ( $item ['label'] );

      if ( $item ['kind'] == 'prev' or $item ['kind'] == 'next' ) {

        $aria = padChartAttr ( ( $item ['kind'] == 'prev' )
          ? ( $catalog ['pager.previous'] ?? 'Previous page' )
          : ( $catalog ['pager.next']     ?? 'Next page'     ) );

        $out [] = $href
          ? "<a href=\"$href\" rel=\"{$item ['kind']}\" aria-label=\"$aria\">$label</a>"
          : "<span aria-disabled=\"true\" aria-label=\"$aria\">$label</span>";

      }
      elseif ( $item ['kind'] == 'gap'     ) $out [] = "<span class=\"gap\">$label</span>";
      elseif ( $item ['kind'] == 'current' ) $out [] = "<a href=\"$href\" aria-current=\"page\">$label</a>";
      else                                   $out [] = "<a href=\"$href\">$label</a>";

    }

    return '<nav class="pad-datatable-pager" aria-label="' . padChartAttr ( $catalog ['pager.label'] ?? 'Pagination' ) . '">'
         . implode ( '', $out ) . '</nav>';

  }

  // The colours follow the page's color-scheme through light-dark(), as the charts' do; a
  // browser without it keeps the light ones. Written with the first table of the request,
  // its rules hold for every table after it.

  function padDatatableStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'text'   => [ '#1f1f1d', '#ecebe6' ],
               'muted'  => [ '#6b6a66', '#9d9c93' ],
               'line'   => [ '#e4e3df', '#3a3a37' ],
               'head'   => [ '#f4f3ef', '#242422' ],
               'stripe' => [ '#f9f9f7', '#1e1e1c' ],
               'accent' => [ '#2a78d6', '#5598e7' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-datatable-$role:$day;";
      $both  .= "--pad-datatable-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-datatable){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-datatable){{$both}}}"
         . '.pad-datatable{max-width:100%;overflow-x:auto;color:var(--pad-datatable-text)}'
         . '.pad-datatable table{border-collapse:collapse;font-variant-numeric:tabular-nums;line-height:1.4}'
         . '.pad-datatable caption{caption-side:top;text-align:left;font-weight:600;padding:0 0 8px}'
         . '.pad-datatable th,.pad-datatable td{padding:7px 12px;text-align:left;border-bottom:1px solid var(--pad-datatable-line)}'
         . '.pad-datatable thead th{background:var(--pad-datatable-head);font-weight:600;white-space:nowrap;border-bottom-width:2px}'
         . '.pad-datatable .num{text-align:right}'
         . '.pad-datatable-striped tbody tr:nth-child(even){background:var(--pad-datatable-stripe)}'
         . '.pad-datatable tfoot th,.pad-datatable tfoot td{font-weight:600;border-top:2px solid var(--pad-datatable-line);border-bottom:0}'
         . '.pad-datatable thead a{color:inherit;text-decoration:none}'
         . '.pad-datatable thead a:hover,.pad-datatable thead a:focus-visible{color:var(--pad-datatable-accent)}'
         . '.pad-datatable-arrow{margin-left:4px;font-size:.75em;color:var(--pad-datatable-muted)}'
         . '.pad-datatable [aria-sort] .pad-datatable-arrow{color:var(--pad-datatable-accent)}'
         . '.pad-datatable-pager{display:flex;flex-wrap:wrap;gap:4px;margin-top:10px}'
         . '.pad-datatable-pager a,.pad-datatable-pager span{min-width:2em;padding:3px 8px;border:1px solid var(--pad-datatable-line);'
         .   'border-radius:6px;text-align:center;text-decoration:none;color:inherit}'
         . '.pad-datatable-pager a:hover{border-color:var(--pad-datatable-accent)}'
         . '.pad-datatable-pager [aria-current]{background:var(--pad-datatable-accent);border-color:var(--pad-datatable-accent);color:#fff}'
         . '.pad-datatable-pager [aria-disabled],.pad-datatable-pager .gap{opacity:.45;border-color:transparent}'
         . '</style>';

  }

  // A list written with commas, a comma inside quotes or brackets left in its item - the
  // format of a column can have parameters of its own: date:date('j M, Y').

  function padDatatableSplit ( $text ) {

    $items = [];
    $item  = '';
    $depth = 0;
    $quote = '';

    foreach ( mb_str_split ( (string) $text ) as $char ) {

      if ( $quote !== '' ) {
        if ( $char == $quote ) $quote = '';
      }
      elseif ( $char == "'" or $char == '"' ) $quote = $char;
      elseif ( $char == '(' or $char == '[' ) $depth++;
      elseif ( $char == ')' or $char == ']' ) $depth--;
      elseif ( $char == ',' and $depth == 0 ) {
        $items [] = trim ( $item );
        $item     = '';
        continue;
      }

      $item .= $char;

    }

    $items [] = trim ( $item );

    return array_values ( array_filter ( $items, fn ( $one ) => $one !== '' ) );

  }

?>
