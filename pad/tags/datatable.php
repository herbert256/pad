<?php

  // {datatable data='orders'} - the rows as a complete HTML table, lib/datatable.php. The
  // rows come from data= - a {data} store, a page array or a _data file, as for {chart} -
  // or, as a pair, from the content: JSON, YAML, XML or CSV, told apart on sight.
  //
  //   {datatable data='orders', columns='number, customer, total', totals='total'}
  //   {datatable data='orders', format='total:currency(\'EUR\')', sortable, rows=10}
  //
  // columns= names the columns and their order, else every field of the first row;
  // labels= the header of each, else the field name as a headline (order_date is Order
  // Date). A column of numbers is right-aligned, its values with the same decimals and
  // grouped by thousands, unless its name says it identifies (id, number, code, year).
  // format= gives a column a pipe instead - 'total:money, date:date(\'j M\')'. totals=
  // sums columns in a totals row, over every row, not only the page shown.
  //
  // sortable makes each header a link that sorts on the server: ?sort=total&dir=desc on
  // the same page, a column the table shows and nothing else, stable. rows= pages the
  // rows with page links below (?page=2). query= puts a prefix on the three request names
  // - query='orders_' asks orders_sort, orders_dir and orders_page - for two tables on one
  // page. striped shades every other row, caption= names the table.
  //
  // The data= option has already been read into this level's data by level/start.php, and
  // the level would iterate it, writing the table once per row - so the level gets its one
  // default occurrence back. A table without rows answers nothing, and its @else@ shows.

  if ( ! isset ( $padPrm [$pad] ['data'] ) and trim ( (string) $padContent ) === '' and ! isset ( $padPrm [$pad] ['sequence'] ) ) {
    if ( $padCheckSyntax )
      padError ( "the datatable has no data - data='name' or the rows between {datatable} and {/datatable}" );
    return '';
  }

  $padDatatableRows = array_values ( array_map ( 'padChartRow', (array) padChartRows ( $padContent, 'datatable' ) ) );
  $padContent       = '';
  $padData [$pad]   = padDefaultData ();

  $padDatatableFields  = padDatatableColumns ( $padDatatableRows, padTagParm ( 'columns' ) );
  $padDatatableLabels  = padDatatableSplit ( padTagParm ( 'labels' ) );
  $padDatatableTotals  = padChartFields ( padTagParm ( 'totals' ) );
  $padDatatableFormats = [];
  $padDatatableQuery   = trim ( (string) padTagParm ( 'query' ) );
  $padDatatablePerPage = padTagParm ( 'rows', 0 );

  // Every name an option gives must be a column the table shows.

  $padDatatableKnown = [];

  foreach ( $padDatatableRows as $padDatatableRow )
    $padDatatableKnown += array_flip ( array_map ( 'strval', array_keys ( $padDatatableRow ) ) );

  if ( $padCheckSyntax and $padDatatableRows )
    foreach ( $padDatatableFields as $padDatatableField )
      if ( ! isset ( $padDatatableKnown [$padDatatableField] ) )
        padError ( "the datatable has no column '" . padMakeSafe ( $padDatatableField, 40 ) . "' - "
                 . implode ( ', ', array_keys ( $padDatatableKnown ) ) );

  if ( $padCheckSyntax and $padDatatableLabels and count ( $padDatatableLabels ) != count ( $padDatatableFields ) )
    padError ( 'the datatable has ' . count ( $padDatatableFields ) . ' columns and ' . count ( $padDatatableLabels ) . ' labels' );

  foreach ( padDatatableSplit ( padTagParm ( 'format' ) ) as $padDatatableOne ) {

    $padDatatableAt = strpos ( $padDatatableOne, ':' );

    if ( $padDatatableAt === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "a format of the datatable is written column:function, not '" . padMakeSafe ( $padDatatableOne, 40 ) . "'" );
      continue;
    }

    $padDatatableFormats [ trim ( substr ( $padDatatableOne, 0, $padDatatableAt ) ) ] = trim ( substr ( $padDatatableOne, $padDatatableAt + 1 ) );

  }

  foreach ( [ 'totals' => $padDatatableTotals, 'format' => array_keys ( $padDatatableFormats ) ] as $padDatatableOption => $padDatatableNames )
    foreach ( $padDatatableNames as $padDatatableField )
      if ( ! in_array ( (string) $padDatatableField, $padDatatableFields, TRUE ) and $padCheckSyntax )
        padError ( "the $padDatatableOption of the datatable names '" . padMakeSafe ( $padDatatableField, 40 ) . "', which is no column - "
                 . implode ( ', ', $padDatatableFields ) );

  if ( $padDatatablePerPage !== 0 and ( ! ctype_digit ( (string) $padDatatablePerPage ) or $padDatatablePerPage < 1 ) ) {
    if ( $padCheckSyntax )
      padError ( "the rows of the datatable are a number of 1 or more, not '" . padMakeSafe ( $padDatatablePerPage, 20 ) . "'" );
    $padDatatablePerPage = 0;
  }

  if ( ! $padDatatableRows )
    return '';

  // The columns, and the sums of the totals= ones over every row.

  $padDatatableColumns = [];
  $padDatatableSums    = [];

  foreach ( $padDatatableFields as $padDatatableIndex => $padDatatableField ) {

    $padDatatableNumeric = padDatatableNumeric ( $padDatatableRows, $padDatatableField );
    $padDatatableTotal   = in_array ( $padDatatableField, $padDatatableTotals, TRUE );

    if ( $padDatatableTotal and ! $padDatatableNumeric ) {
      if ( $padCheckSyntax )
        padError ( "the column '" . padMakeSafe ( $padDatatableField, 40 ) . "' of the datatable holds values that are no number, so it has no total" );
      $padDatatableTotal = FALSE;
    }

    if ( $padDatatableTotal ) {
      $padDatatableSums [$padDatatableField] = 0;
      foreach ( $padDatatableRows as $padDatatableRow )
        if ( is_numeric ( trim ( (string) ( $padDatatableRow [$padDatatableField] ?? '' ) ) ) )
          $padDatatableSums [$padDatatableField] += trim ( (string) $padDatatableRow [$padDatatableField] ) + 0;
    }

    $padDatatableColumns [] = [
      'field'    => $padDatatableField,
      'label'    => $padDatatableLabels [$padDatatableIndex] ?? padStrHeadline ( $padDatatableField ),
      'numeric'  => $padDatatableNumeric,
      'decimals' => padDatatableDecimals ( $padDatatableRows, $padDatatableField ),
      'plain'    => padDatatableIdentifier ( $padDatatableField ),
      'format'   => $padDatatableFormats [$padDatatableField] ?? '',
      'total'    => $padDatatableTotal
    ];

  }

  // The order the request asks for: a column the table shows, else the rows as they came.

  $padDatatableSortable = (bool) padTagParm ( 'sortable', FALSE );
  $padDatatableSortKey  = $padDatatableQuery . 'sort';
  $padDatatableDirKey   = $padDatatableQuery . 'dir';
  $padDatatablePageKey  = $padDatatableQuery . 'page';
  $padDatatableSort     = '';
  $padDatatableDesc     = FALSE;

  if ( $padDatatableSortable ) {

    $padDatatableAsked = $_GET [$padDatatableSortKey] ?? '';

    if ( is_string ( $padDatatableAsked ) and in_array ( $padDatatableAsked, $padDatatableFields, TRUE ) ) {
      $padDatatableSort = $padDatatableAsked;
      $padDatatableDesc = ( ( $_GET [$padDatatableDirKey] ?? '' ) === 'desc' );
      $padDatatableRows = padDatatableSort ( $padDatatableRows, $padDatatableSort, $padDatatableDesc );
    }

  }

  // One page of the rows, and the links to the others - the {pager}'s items.

  $padDatatablePager = [];

  if ( $padDatatablePerPage ) {

    $padDatatablePerPage = (int) $padDatatablePerPage;
    $padDatatablePages   = (int) ceil ( count ( $padDatatableRows ) / $padDatatablePerPage );
    $padDatatablePage    = $_GET [$padDatatablePageKey] ?? 1;
    $padDatatablePage    = ( is_string ( $padDatatablePage ) and ctype_digit ( $padDatatablePage ) ) ? (int) $padDatatablePage : 1;
    $padDatatablePage    = max ( 1, min ( $padDatatablePages, $padDatatablePage ) );

    $padDatatablePager = padPagerItems ( [ 'page' => $padDatatablePage, 'rows' => $padDatatablePerPage,
                                           'total' => count ( $padDatatableRows ), 'count' => '' ],
                                         2, $padDatatablePageKey );

    $padDatatableRows = array_slice ( $padDatatableRows, ( $padDatatablePage - 1 ) * $padDatatablePerPage, $padDatatablePerPage );

  }

  return padDatatableHtml ( [
    'columns'  => $padDatatableColumns,
    'rows'     => $padDatatableRows,
    'totals'   => (bool) $padDatatableSums,
    'sums'     => $padDatatableSums,
    'caption'  => (string) padTagParm ( 'caption' ),
    'striped'  => (bool) padTagParm ( 'striped', FALSE ),
    'sortable' => $padDatatableSortable,
    'sort'     => $padDatatableSort,
    'desc'     => $padDatatableDesc,
    'sortKey'  => $padDatatableSortKey,
    'dirKey'   => $padDatatableDirKey,
    'pageKey'  => $padDatatablePageKey,
    'pager'    => $padDatatablePager
  ] );

?>
