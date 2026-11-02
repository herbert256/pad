<?php

  // {table:standard data='orders'} - the rows as a complete HTML table, subs/tables/_lib/table.php. The
  // rows come from data= - a {format:auto} store, a page array or a _data file, as for {chart:bar} -
  // or, as a pair, from the content: JSON, YAML, XML or CSV, told apart on sight.
  //
  //   {table:standard data='orders', columns='number, customer, total', totals='total'}
  //   {table:standard data='orders', format='total:currency(\'EUR\')', sortable, rows=10}
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
      padError ( "the datatable has no data - data='name' or the rows between {table:standard} and {/table:standard}" );
    return '';
  }

  $padDatatableRows = array_values ( array_map ( 'padChartRow', (array) padChartRows ( $padContent, 'datatable' ) ) );
  $padHandDone [$pad] = TRUE; // row=, group= and rows= belong to this renderer.
  $padContent       = '';
  $padData [$pad]   = padDefaultData ();

  $padTableFilterFields = padChartFields ( padTagParm ( 'filters' ) );
  $padTableGroupFields = padChartFields ( padTagParm ( 'group' ) );
  $padTableGroups = [];
  $padTableQuery = padTagParm ( 'query' );
  if ( ! is_scalar ( $padTableQuery ) ) {
    if ( $padCheckSyntax ) padError ( 'the query prefix of a table must be text' );
    $padTableQuery = '';
  }
  $padTableQuery = trim ( (string) $padTableQuery );
  padTableFieldCheck ( $padDatatableRows, $padTableFilterFields, 'filters' );
  $padDatatableRows = padTableFilter ( $padDatatableRows, $padTableFilterFields, $padTableQuery );
  $padTablePivot = in_array ( $padTableKind, [ 'pivot', 'matrix' ], TRUE );
  if ( $padTablePivot ) {
    $padTableRow = padTagParm ( 'row' );
    $padTableColumn = padTagParm ( 'column' );
    $padTableValue = padTagParm ( 'value' );
    $padTableAggregate = padTagParm ( 'aggregate', $padTableKind === 'matrix' ? 'count' : 'sum' );
    if ( ! is_string ( $padTableAggregate ) or ! in_array ( $padTableAggregate, [ 'sum', 'count', 'avg', 'min', 'max' ], TRUE ) ) {
      if ( $padCheckSyntax ) padError ( 'the aggregate of a table must be sum, count, avg, min or max' );
      return '';
    }
    if ( ! is_string ( $padTableRow ) or $padTableRow === '' or ! is_string ( $padTableColumn ) or $padTableColumn === ''
         or ( $padTableAggregate !== 'count' and ( ! is_string ( $padTableValue ) or $padTableValue === '' ) ) ) {
      if ( $padCheckSyntax ) padError ( 'a pivot or matrix requires row= and column= fields, and value= unless aggregate=count' );
      return '';
    }
    padTableFieldCheck ( $padDatatableRows, array_merge ( [ $padTableRow, $padTableColumn ], $padTableAggregate === 'count' ? [] : [ $padTableValue ] ), 'pivot' );
    [ $padDatatableRows, $padTablePivotFields, $padTablePivotLabels ] = padTablePivot ( $padDatatableRows, $padTableRow, $padTableColumn, $padTableValue, $padTableAggregate );
  }
  if ( $padTableKind === 'grouped' ) {
    if ( ! $padTableGroupFields and $padCheckSyntax ) padError ( 'a grouped table requires group= with one or more fields' );
    padTableFieldCheck ( $padDatatableRows, $padTableGroupFields, 'group' );
  } else $padTableGroupFields = [];

  $padDatatableFields  = $padTablePivot ? $padTablePivotFields : padDatatableColumns ( $padDatatableRows, padTagParm ( 'columns' ) );
  $padDatatableLabels  = $padTablePivot ? $padTablePivotLabels : padDatatableSplit ( padTagParm ( 'labels' ) );
  $padDatatableTotals  = padChartFields ( padTagParm ( 'totals' ) );
  $padDatatableFormats = [];
  $padDatatableQuery   = $padTableQuery;
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
    return $padTableFilterFields ? padTableExtraStyle () . padTableFilterForm ( $padTableFilterFields, $padDatatableQuery ) . '<p class="pad-table-empty">No matching rows.</p>' : '';

  // The columns, and the sums of the totals= ones over every row.

  $padDatatableColumns = [];
  $padDatatableSums    = [];

  foreach ( $padDatatableFields as $padDatatableIndex => $padDatatableField ) {

    $padDatatableNumeric = ( $padTablePivot and $padDatatableField === 'row_label' ) ? FALSE : padDatatableNumeric ( $padDatatableRows, $padDatatableField );
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

  // Sort within each group, keeping groups together; subtotals describe the complete
  // filtered group even when its rows span several pages.
  if ( $padTableGroupFields ) {
    $padTableGroups = padTableGroups ( $padDatatableRows, $padTableGroupFields, $padDatatableTotals );
    $padDatatableRows = array_merge ( ...array_column ( $padTableGroups, 'rows' ) );
  }
  $padTableHeat = [];
  if ( $padTableKind === 'matrix' )
    foreach ( $padDatatableRows as $padTableHeatRow )
      foreach ( $padTableHeatRow as $padTableHeatField => $padTableHeatValue )
        if ( $padTableHeatField !== 'row_label' and is_numeric ( $padTableHeatValue ) ) $padTableHeat [] = $padTableHeatValue;

  // One page of the rows, and the links to the others - the {pad:pager}'s items.

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

  return ( $padTableKind !== 'standard' || $padTableFilterFields ? padTableExtraStyle () : '' )
    . padTableFilterForm ( $padTableFilterFields, $padDatatableQuery ) . padDatatableHtml ( [
    'groupFields' => $padTableGroupFields,
    'groups' => $padTableGroups,
    'heat' => $padTableHeat ? [ min ( $padTableHeat ), max ( $padTableHeat ) ] : [],
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
