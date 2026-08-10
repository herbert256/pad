<?php

  // The PAD select subsystem: builds and runs the SQL behind a {select:table} tag, so a
  // template can walk related tables without writing any SQL.
  //
  // padSelect is the entry point. Every clause is taken from the tag's parameters, falling
  // back to the table's declaration in $padSelect (padSelectGetDB, which also inherits
  // from a base= table). The clause builders each own one part: padSelectStart
  // (all/distinct), padSelectFields with padSelectAddFields (field list and aliases),
  // padSelectJoin with padSelectJoinAdd (joins and their on conditions), padSelectGroup
  // (group by, with rollup), padSelectOrder, padSelectLimit (page/rows into an offset,
  // marked padDone so an outer pager does not apply it twice), padSelectUnion (recurses
  // into padSelect with $unionBuild set to get the union member instead of the result),
  // padSelectKeys and padSelectField (backtick quoting).
  //
  // padSelectWhere is where the automatic joining happens: besides the given where= it
  // adds a condition for each key set at this level, and then walks up the level stack
  // looking for enclosing select tags. For each it consults $padRelations (in both
  // directions, and along base= chains) and, via padSelectWhereRelation,
  // padSelectWhereKeys and padSelectWhereAdd, constrains this query by the current row of
  // the outer one - that is what makes a nested {select:} follow the relation.
  //
  // Statements are collected in $_SELECT and $_UNION; the result comes back through db().

  function padSelect ( $table, $unionBuild = 0 ) {

    global $_SELECT, $_UNION, $pad, $padPrm, $padHtmlAttrJson;

    global     $start,$group,$limit,$where,$join,$order,$union;

    $parms = padSelectGetDB ($table);

    // A union member is built from its declaration alone: the tag's own options belong to
    // the outer query, and reading them here made a tag-side union='member' find itself in
    // the member's build and recurse without end.

    $prm = ( $unionBuild ) ? [] : ( $padPrm [$pad] ?? [] );

    // Every select option the query was given goes on the xref record - the declaration's
    // words too, though the source filter keeps only what the page itself says.

    global $padInfoXref;

    if ( ( $padInfoXref ?? FALSE ) and function_exists ( 'padInfoXref' ) )
      foreach ( array_keys ( (array) $prm + (array) $parms ) as $padSelName )
        if ( file_exists ( PAD . "select/types/$padSelName.php" ) )
          padInfoXref ( 'options', 'select', $padSelName );

    // The select options the query consumes are read right here, so they are marked read
    // right here - the strict unread-option sweep looks for the mark.

    foreach ( array_keys ( (array) $prm ) as $padSelName )
      if ( file_exists ( PAD . "select/types/$padSelName.php" )
           or in_array ( $padSelName, [ 'all', 'type', 'page', 'rows' ] ) )
        padDone ( $padSelName );

    $db           = $prm ['db']           ?? $parms ['db']          ?? $table;
    $all          = $prm ['all']          ?? $parms ['all']         ?? 0;
    $distinct     = $prm ['distinct']     ?? $parms ['distinct']    ?? 0;
    $distinctrow  = $prm ['distinctrow']  ?? $parms ['distinctrow'] ?? 0;
    $keys         = $prm ['key']          ?? $parms ['key']         ?? '';
    $fields       = $prm ['fields']       ?? $parms ['fields']      ?? '*';
    $type         = $prm ['type']         ?? $parms ['type']        ?? 'array';
    $where        = $prm ['where']        ?? $parms ['where']       ?? '';
    $group        = $prm ['group']        ?? $parms ['group']       ?? '';
    $rollup       = $prm ['rollup']       ?? $parms ['rollup']      ?? 0;
    $having       = $prm ['having']       ?? $parms ['having']      ?? '';
    $join         = $prm ['join']         ?? $parms ['join']        ?? [];
    $union        = $prm ['union']        ?? $parms ['union']       ?? '';
    $order        = $prm ['order']        ?? $parms ['order']       ?? '';
    $page         = $prm ['page']         ?? $parms ['page']        ?? 0;
    $rows         = $prm ['rows']         ?? $parms ['rows']        ?? 0;
    $htmlAttrJson = $prm ['htmlAttrJson'] ?? $parms ['rows']        ?? 0;

    if ( ! $padHtmlAttrJson and $htmlAttrJson ) {
      $padHtmlAttrJson = $htmlAttrJson;
      $type            = $htmlAttrJson;
    }

    $start  = padSelectStart  ( $all, $distinct, $distinctrow);
    $group  = padSelectGroup  ( $group, $rollup );
    $having = padSelectHaving ( $having );
    $limit  = padSelectLimit  ( $rows, $page );
    $where  = padSelectWhere  ( $where, $table, $keys );
    $fields = padSelectFields ( $fields, $db );
    $join   = padSelectJoin   ( $join, $fields );
    $keys   = padSelectKeys   ( $keys );
    $order  = padSelectOrder  ( $order, $join, $keys );

    // The outer query's parts are folded into text before the union members are built:
    // the composition variables are globals - the app dump reads them - and a member's
    // build writes its own parts into the same names, so a union query composed after it
    // carried the member's where instead of its own.

    $head = "$start $fields from $db $join $where $group $having";
    $tail = "$order $limit";

    $union  = padSelectUnion  ( $union );

    $_UNION [] = $union;

    $base  = "$head $union";
    $sql   = "$type $base $tail";
    $union = "union select $base";

    $_SELECT [] = $sql;

    if ($unionBuild)
      return $union;
    else
      return db ( $sql );

  }

  function padSelectKeys ( $keys ) {

    if ( ! $keys )
      return '';

    foreach ( padExplode ( $keys, ',' ) as $field )
      $set [] = "`$field`";

    return implode( ',' , $set );

  }

  function padSelectStart ( $all,  $distinct, $distinctrow ) {

    if     ($all)         return 'ALL';
    elseif ($distinct)    return 'distinct';
    elseif ($distinctrow) return 'distinctrow';
    else                  return '';

  }

  function padSelectGroup ( $group, $rollup ) {

    if ($group)
      $group = "group by $group";

    if ($rollup)
      $group .= ' with rollup';

    return $group;

  }

  // The keyword comes from here, as where and group get theirs from their builders: the
  // having= option carries the condition alone. It was concatenated raw before, which no
  // spelling of the option could satisfy - the keyword had nowhere to come from.

  function padSelectHaving ( $having ) {

    return ( $having ) ? "having $having" : '';

  }

  function padSelectOrder ( $order, $joinSQL, $keys ) {

    if     ( $order              ) return 'order by ' . $order;
    elseif ( !$joinSQL and $keys ) return 'order by ' . $keys;
    else                           return '';

  }

  function padSelectLimit ( $rows, $page ) {

    global $padDone;

    $limit = '';

    if ( ! isset($padDone['page']) or ! isset($padDone['rows']))

      if ($page or $rows) {

        if (!$rows) $rows = 10;
        if (!$page) $page = 1;

        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";

        padDone ('page', TRUE);
        padDone ('rows', TRUE);

      }

    return $limit;

  }

  function padSelectWhere ( $where, $table, $keys ) {

    global $pad, $padRelations, $padCurrent, $padTag, $padType, $padSetLvl;

    if ($where)
      $where = 'where (' . $where . ')';

    foreach ( padExplode ( $keys, ',' ) as $field )
      if ( isset ( $padSetLvl [$pad] [$field] ) )
        padSelectWhereAdd ( $where, "$field", $GLOBALS [$field] );

    for ( $i=$pad-1; $i; $i-- )
      if ( $padType [$i] == 'select' ) {

        $relation = $padTag [$i] ;
        $parms    = padSelectGetDB ( $relation ) ;

        padSelectWhereRelation ( $where, $table, $relation, $padCurrent [$i] );

        while ( isset ( $parms ['base'] ) ) {
          $relation = $parms ['base'];
          $parms    = padSelectGetDB ( $relation ) ;
          padSelectWhereRelation ( $where, $table, $relation, $padCurrent [$i] );
        }

      }

    return $where;

  }

  function padSelectWhereRelation ( &$where, $table, $relation, $data ) {

    global $padRelations;

    if  ( isset ( $padRelations [$relation] [$table] ) )
      padSelectWhereKeys ( $where, $padRelations [$relation] [$table], $data, 0 );
    elseif ( isset ( $padRelations [$table] [$relation] ) )
      padSelectWhereKeys ( $where, $padRelations [$table] [$relation], $data, 1);

  }

  function padSelectWhereKeys ( &$where, $keys, $data, $type ) {

    if ( is_array ($keys) )
      foreach ( $keys as $key => $value )
        if ( $type )
          padSelectWhereAdd ( $where, $key, $data [ $value ] );
        else
          padSelectWhereAdd ( $where, $value, $data [ $key ] );
    else
      foreach ( padExplode ( $keys, ',' ) as $field )
        padSelectWhereAdd ( $where, $field, $data [ $field ] );

  }

  function padSelectWhereAdd  (&$where, $field, $value) {

    $add = padSelectField ($field) . ' = ' . "'";

    if ( strpos ( $where, $add ) !== FALSE )
      return;

    if ($where) $where .= ' and ';
    else        $where  = 'where ';

    $where .= $add . padEscape ($value) . "'";

  }

  function padSelectFields  ( $fields, $db ) {

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      padSelectAddFields ( $fields, $db, $work );
    }

    return $fields;

  }

 function padSelectJoin ( $join, &$fields ) {

    $joinSQL = '';

    if ( ! is_array($join) and $join )
      $joinSQL = ' join ' . $join . ' ';

    if ( is_array($join) and count($join) ) {

      if ( ! is_array($join[array_key_first($join)]))
        $join = [ 0 => $join];

      foreach ($join as $key => $value) {

        foreach ($value as $xtype => $table)
          break;

        $joinTable = padSelectGetDB ( $table ) ;
        padSelectAddFields ($fields, $joinTable ['db'] , $joinTable ['fields'] );
        $joinSQL .= ' ' . $xtype .  ' join ' . $joinTable ['db'] . ' ';

        if ( isset($value ['key']) ) {
          $joinSQL .= ' on ';
          $joinSQL .= padSelectJoinAdd ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
        }

      }

    }

    return $joinSQL;

  }

  function padSelectJoinAdd ($keys1, $db, $keys2) {

    $where = '';

    $values1 = padExplode ($keys1, ',');
    $values2 = padExplode ($keys2, ',');

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';

      $where .= padSelectField($v) . ' = `' . $db . '`.' . padSelectField($values2[$k]);

    }

    return $where;

  }

  function padSelectUnion ( $union ) {

    $unionSQL = '';

    if ( is_array($union) )
      $unionQ = $union;
    else {
      $unionQ = array();
      if ($union)
        $unionQ [] = $union;
    }

    foreach ($unionQ as $key)
      $unionSQL .= ' ' . padSelect ($key, 1);

    return $unionSQL;

  }

  function padSelectField ($field) {

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }

  function padSelectAddFields (&$result, $table, $fields) {

    if ( is_array($fields) ) {
      foreach ($fields as $key => $value) {
        if ($result)
          $result .= ',';
        $result .= ' ' . $table . '.' . $key . ' as ' . $value;
      }
    } else {
      if ($result)
        $result .= ',';
      $result .= $fields;
    }

  }

  function padSelectGetDB ($table) {

    global $padSelect;

    if ( ! isset ( $padSelect [$table] ) )
      return [ 'db' => $table ];

    $parms = $padSelect [$table];

    if ( isset($parms['base']) and isset($padSelect [$parms['base']]) )
      foreach($padSelect [$parms['base']] as $key => $value)
        if ( ! isset($parms[$key]) )
          $parms[$key] = $value;

    if ( ! isset ( $parms ['db'] ) )
      if ( isset($parms['base']) )
        $parms ['db'] = $parms['base'];
      else
        $parms ['db'] = $table;

    if ( ! isset ( $parms ['key'] ) )
      $parms ['key'] = '';

    return $parms;

  }

?>