<?php

  // Handles the group option: folds the tag's rows into one occurrence per distinct value
  // of a field - {orders group='customer', sum='total'} - so a report can print a heading
  // and a subtotal per customer and the customer's own rows below it, where {ifchanged}
  // could only print the heading and the totals needed a sequence action or SQL.
  //
  // Each group occurrence holds the grouping field(s) with the group's value, count - the
  // number of rows in the group - and rows, the group's own rows as nested data: {rows}
  // inside the level iterates them, and there a field is the row's own value again. The
  // modifiers sum, avg, min and max name the fields to aggregate over the group's rows;
  // each aggregate is stored under the field's own name - {$total} is the sum of total -
  // and under the aggregate joined to it - {$sum_total}, {$avg_total} - so one field can
  // be summed and averaged at once (the own name then holds the one written first).
  //
  // Groups come in the order their first row appears, so a sort written before the group
  // orders the groups and the rows inside them, and a sort written after it orders the
  // groups - on their aggregates as well: sort='total DESC'. first, page and where written
  // after the group count groups. Several fields - group='year, month' - group on the
  // combination. sum skips values that are not numeric and is rounded to the most decimals
  // its values have, so 10.10 and 20.20 add up to 30.3 and not to a float's 30.299999...;
  // avg divides by the numeric values it saw; min and max compare as PHP does - numbers as
  // numbers, other text as text - and leave missing and empty values out. A group without
  // any value to aggregate has '' for avg, min and max, 0 for sum.
  //
  // A select table has a group of its own - SQL's group by, applied by the query - so its
  // rows are left alone here, as the where handler leaves them.

  if ( $padType [$pad] == 'select' )
    return;

  include_once PAD . 'handling/_lib/group.php';

  $padGroupKeys = preg_split ( '/[\s,;]+/', trim ( (string) $padHandParm ), -1, PREG_SPLIT_NO_EMPTY );

  if ( $padHandParm === TRUE or ! $padGroupKeys ) {

    if ( $padCheckSyntax )
      padError ( "the group option needs the field to group on - group='customer'" );

    return;

  }

  // count and rows are the names a group gives its own count and its own rows: grouped on a
  // field of that name, the group's count took the value's place - {xs group='count'} showed
  // 1 for every group and the values grouped on were nowhere.

  if ( $padCheckSyntax )
    foreach ( $padGroupKeys as $padGroupKey )
      if ( ! padHandFieldSeen ( $padData [$pad], $padGroupKey ) )
        return padError ( "the group field '$padGroupKey' is in no row" );
      elseif ( in_array ( $padGroupKey, [ 'count', 'rows' ], TRUE ) )
        return padError ( "group='$padGroupKey' names a field each group sets itself - its count of rows and its rows are count and rows" );

  // The aggregates, in the order they were written on the tag: that order decides which of
  // two aggregates of one field keeps the field's own name.

  $padGroupAggs = [];

  foreach ( $padParms [$pad] as $padGroupParm )
    if ( $padGroupParm ['padPrmKind'] == 'option' and in_array ( $padGroupParm ['padPrmName'], [ 'sum', 'avg', 'min', 'max' ] ) ) {

      $padGroupFun = $padGroupParm ['padPrmName'];
      $padGroupVal = $padPrm [$pad] [$padGroupFun] ?? '';

      padDone ( $padGroupFun );

      $padGroupList = preg_split ( '/[\s,;]+/', trim ( $padGroupVal === TRUE ? '' : (string) $padGroupVal ), -1, PREG_SPLIT_NO_EMPTY );

      if ( ! $padGroupList and $padCheckSyntax )
        padError ( "the $padGroupFun option needs the field to aggregate - $padGroupFun='total'" );

      foreach ( $padGroupList as $padGroupField )
        $padGroupAggs [] = [ $padGroupFun, $padGroupField ];

    }

  // One bucket per distinct key, in the order of first appearance.

  $padGroups = [];

  foreach ( $padData [$pad] as $padGroupRow ) {

    if ( ! is_array ( $padGroupRow ) )
      $padGroupRow = [];

    // A value is told by its text, as dedup tells it: 5 from a database row and '5' from a
    // CSV row are one value, where serialize kept the integer and the string apart and the
    // same customer came out as two groups.

    $padGroupId = [];
    foreach ( $padGroupKeys as $padGroupKey ) {
      $padGroupOne   = $padGroupRow [$padGroupKey] ?? '';
      $padGroupId [] = is_scalar ( $padGroupOne ) ? (string) $padGroupOne : $padGroupOne;
    }

    $padGroupId = serialize ( $padGroupId );

    if ( ! isset ( $padGroups [$padGroupId] ) ) {

      $padGroups [$padGroupId] = [];

      foreach ( $padGroupKeys as $padGroupKey )
        $padGroups [$padGroupId] [$padGroupKey] = $padGroupRow [$padGroupKey] ?? '';

      $padGroups [$padGroupId] ['count'] = 0;
      $padGroups [$padGroupId] ['rows']  = [];

    }

    $padGroups [$padGroupId] ['count'] ++;
    $padGroups [$padGroupId] ['rows'] [] = $padGroupRow;

  }

  foreach ( $padGroups as $padGroupId => $padGroup ) {

    $padGroupOwn = [];

    foreach ( $padGroupAggs as list ( $padGroupFun, $padGroupField ) ) {

      $padGroupValue = padGroupAggregate ( $padGroupFun, array_column ( $padGroup ['rows'], $padGroupField ) );

      $padGroup [ $padGroupFun . '_' . $padGroupField ] = $padGroupValue;

      // An aggregate of a field named count or rows has only its joined name, sum_rows: under
      // its own name it took the place of the group's count or of its rows - sum='rows' made
      // the rows a number, and {rows}...{/rows} failed on it.

      if ( in_array ( $padGroupField, [ 'count', 'rows' ], TRUE ) )
        continue;

      if ( ! isset ( $padGroupOwn [$padGroupField] ) or $padGroupOwn [$padGroupField] == $padGroupFun ) {
        $padGroup [$padGroupField] = $padGroupValue;
        $padGroupOwn [$padGroupField] = $padGroupFun;
      }

    }

    // rows stays the last field, after the aggregates.

    $padGroupRows = $padGroup ['rows'];
    unset ( $padGroup ['rows'] );
    $padGroup ['rows'] = $padGroupRows;

    $padGroups [$padGroupId] = $padGroup;

  }

  $padData [$pad] = array_values ( $padGroups );

?>
