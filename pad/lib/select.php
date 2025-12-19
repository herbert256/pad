<?php

  function padSelect ( $table, $unionBuild = 0 ) {

    global $pad, $padPrm;

    $parms = padSelectGetDB ($table);

    $db          = $padPrm [$pad] ['db']          ?? $parms ['db']          ?? $table;
    $all         = $padPrm [$pad] ['all']         ?? $parms ['all']         ?? 0;
    $distinct    = $padPrm [$pad] ['distinct']    ?? $parms ['distinct']    ?? 0;
    $distinctrow = $padPrm [$pad] ['distinctrow'] ?? $parms ['distinctrow'] ?? 0;
    $keys        = $padPrm [$pad] ['key']         ?? $parms ['key']         ?? '';
    $fields      = $padPrm [$pad] ['fields']      ?? $parms ['fields']      ?? '*';
    $type        = $padPrm [$pad] ['type']        ?? $parms ['type']        ?? 'array';
    $where       = $padPrm [$pad] ['where']       ?? $parms ['where']       ?? '';
    $group       = $padPrm [$pad] ['group']       ?? $parms ['group']       ?? '';
    $rollup      = $padPrm [$pad] ['rollup']      ?? $parms ['rollup']      ?? 0;
    $having      = $padPrm [$pad] ['having']      ?? $parms ['having']      ?? '';
    $join        = $padPrm [$pad] ['join']        ?? $parms ['join']        ?? [];
    $union       = $padPrm [$pad] ['union']       ?? $parms ['union']       ?? '';
    $order       = $padPrm [$pad] ['order']       ?? $parms ['order']       ?? '';
    $page        = $padPrm [$pad] ['page']        ?? $parms ['page']        ?? 0;
    $rows        = $padPrm [$pad] ['rows']        ?? $parms ['rows']        ?? 0;

    $start = padSelectStart ( $all, $distinct, $distinctrow);
    $group = padSelectGroup ( $group, $rollup );
    $limit = padSelectLimit ( $rows, $page );
    $where = padSelectWhere ( $where, $table, $keys );
    $join  = padSelectJoin  ( $join, $fields );
    $order = padSelectOrder ( $order, $join, $keys );
    $union = padSelectUnion ( $union );

    if ($unionBuild)
      return "union select $start $fields from $db $join $where $group $having $union";
    else
      return db ("$type $start $fields from $db $join $where $group $having $union $order $limit");

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
  

  function padSelectWhere ( &$where, $table, $keys1 ) {

    global $pad, $padSelectRelations, $padCurrent, $padType;

    if ($where)
      $where = 'where (' . $where . ')';

    foreach ( padExplode ( $keys, ',' ) as $field ) 
      if ( isset ( $padSetLvl [$pad] [$field] ) )
        padSelectWhereAdd ( $where, $field, $GLOBALS [$field] );
  
    for ( $i=$pad-1; $i; $i-- )
      if ( $padType [$i] == 'select' ) {

        $relation = $padTag [$i] ;
        $keys2    = padSelectGetDB ( $relations ) ['keys'];

        if     ( isset ( $padSelectRelations [$relation] [$table] ) ) 
          padSelectWhereGo ( $where, $keys2, $keys1, $padCurrent [$i]
           );
        elseif ( isset ( $padSelectRelations [$table] [$relation] ) ) 
          padSelectWhereGo ( $where, $keys1, $keys2, $padCurrent [$i] );

      }

  }


  function padSelectWhereGo ( &$where, $keys1, $key2, $data ) {
  
    $k1 = padExplode ( $keys1, ',' );
    $k2 = padExplode ( $keys2, ',' );

    foreach ( $k1 as $k => $v )
       padSelectWhereAdd ( $where, $v, $data [ $k2 [$k] ] );
  
  }


  function padSelectWhereAdd  (&$where, $field, $value) {

    $add = padSelectField ($field) . ' = ' . "'";

    if ( strpos ( $where, $add ) !== FALSE )
      return;

    if ($where) $where .= ' and ';
    else        $where  = 'where ';

    $where .= $add . padEscape ($value) . "'";

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


  function padSelectUnion ( $union) {

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

    global $padSelectTables;

    if ( ! isset ( $padSelectTables [$table] ) )
      return [ 'db' => $table ];

    $parms = $padSelectTables [$table];

    if ( isset($parms['base']) and isset($padSelectTables [$parms['base']]) )
      foreach($padSelectTables [$parms['base']] as $key => $value)
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