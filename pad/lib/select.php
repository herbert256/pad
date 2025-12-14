<?php


  /**
   * Builds and executes a SQL SELECT query from table definition.
   *
   * Constructs SQL from parameters (fields, where, join, order, etc.)
   * using table definitions from $padSelectTables. Handles relations,
   * pagination, and union queries.
   *
   * @param string $table      The table name or definition key.
   * @param int    $unionBuild If 1, returns SQL for UNION instead of executing.
   *
   * @return array|string Query results or SQL fragment for unions.
   *
   * @global int   $pad      Current processing level.
   * @global array $padPrm   Parameters per level.
   */
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

    $start = padSelectTablestart ( $all, $distinct, $distinctrow);
    $group = padSelectGroup ( $group, $rollup );
    $limit = padSelectLimit ( $rows, $page );
    $where = padSelectWhere ( $where, $fields, $table, $keys, $db );
    $join  = padSelectJoin  ( $join, $fields );
    $order = padSelectOrder ( $order, $join, $keys );
    $union = padSelectUnion ( $union );

    if ($unionBuild)
      return "union select $start $fields from $db $join $where $group $having $union";
    else
      return db ("$type $start $fields from $db $join $where $group $having $union $order $limit");

  }


  /** Returns SQL SELECT modifier (ALL, DISTINCT, DISTINCTROW). */
  function padSelectTablestart ( $all,  $distinct, $distinctrow ) {

    if     ($all)         return 'ALL';
    elseif ($distinct)    return 'distinct';
    elseif ($distinctrow) return 'distinctrow';
    else                  return '';

  }


  /** Builds GROUP BY clause with optional ROLLUP. */
  function padSelectGroup ( $group, $rollup ) {

    if ($group)
      $group = "group by $group";

    if ($rollup)
      $group .= ' with rollup';

    return $group;

  }


  /** Builds ORDER BY clause from order parameter or keys. */
  function padSelectOrder ( $order, $joinSQL, $keys ) {

    if     ( $order              ) return 'order by ' . $order;
    elseif ( !$joinSQL and $keys ) return 'order by ' . $keys;
    else                           return '';

  }


  /** Builds LIMIT clause for pagination. */
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

  /** Builds WHERE clause from relations and key fields. */
  function padSelectWhere ( $where, &$fields, $table, $keys, $db ) {

    global $padSelectRelations;

    if ($where)
      $where = 'where (' . $where . ')';

    if ( isset ( $padSelectRelations [$table] ) )
      foreach ( $padSelectRelations [$table] as $key => $val)
        padSelectGo ( $key, $key, $val, $where );

    foreach ( $padSelectRelations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
        if ( $key2 == $table )
          padSelectGo ( $table, $key, $val, $where );

    $parts = padExplode ($keys, ',');
    foreach($parts as $key)
      if ( padFieldCheck  ($key) )
        padSelectWhereAdd ($where, $key, padFieldValue ($key) );

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      padSelectAddFields ($fields, $db, $work);
    }

    return $where;

  }


  /** Builds JOIN clauses from join parameter array. */
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


  function padSelectGo ( $table, $key, $val, &$where ) {

    global $pad, $padSelectTag, $padData, $padKey;

    $relation = padSelectGetDB ($table);

    if ( ! count ( $relation ) )
      return;

    $parts1 = padExplode ( $relation ['key'], ',');
    $parts2 = ( isset($val['key']) ) ? padExplode($val ['key'], ',') : padExplode($relation ['key'], ',');

    for ( $i=$pad-1; $i; $i-- )
      if ( $padSelectTag[$i] == $key )
        foreach ( $parts2 as $i2 => $key )
          if (  padFieldCheck($key) )
            padSelectWhereAdd ($where, $parts1[$i2], padFieldValue($key) );

  }


  function padSelectWhereAdd (&$where, $field, $value) {

    $add = padSelectField ($field) . ' = ' . "'";

    if ( strpos ( $where, $add ) !== FALSE )
      return;

   if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $add . padEscape ($value) . "'";

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


  function padSelectGetInfo () {

    global $pad, $padSelect, $padSelectTables, $padSelectRelations;

    foreach ($padSelect [$pad] as $table => $value)

      if ( isset ($padSelectRelations [$table]) )

        foreach ( $padSelectRelations [$table] as $rel => $val)

          if ( ! padSelectChk ( $rel ) ) {

            $relation = padSelectGetDB ($rel);
            $where    = $relation ['where'] ?? '';
            $parts1   = padExplode ($relation ['key'], ',');
            $parts2   = ( isset($val['key']) ) ? $val['key'] : $relation ['key'];
            $parts2   = padExplode ($parts2, ',');

            foreach($parts2 as $i => $fld) {

              if ( ! isset ( $fld, $padSelect [$pad] [$table] ) )
                continue 2;

              padSelectWhereAdd ($where, $parts1[$i], $padSelect [$pad] [$table] [$fld] ?? '');

            }

            if ( $where ) {
              $padSelect [$pad] [$rel] = padSelectGet ($relation, $where);
              return TRUE;
            }

          }

    while ( padSelectGetInfoGo () );

    return FALSE;

  }


  function padSelectGetInfoGo () {

    global $padSelectTables, $pad, $padSelect;

    foreach ($padSelectTables as $key => $val)

      if ( ! padSelectChk ($key) and ! isset( $val['virtual'] ) ) {

        $relation = padSelectGetDB ($key);

        $where = '';

        foreach ( padExplode ($relation['key']??'', ',') as $fld)
          if ( ! padFieldCheck ($fld) )
            continue 2;
          else
            padSelectWhereAdd ( $where, $fld, padFieldValue ($fld) );

        if ( $where ) {
          $padSelect [$pad] [$key] = padSelectGet ($relation, $where);
          while ( padSelectGetInfo () ) ;
          return TRUE;
        }

      }

    return FALSE;

  }


  function padSelectChk ($table) {

    global $pad, $padSelect;

    for ( $i=$pad; $i; $i--)
      if ( isset ( $padSelect [$i] [$table] ) )
        return TRUE;

    return FALSE;

  }


  function padSelectGet ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';

    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      padSelectAddFields ($fields, $db, $work);
    }

    return db ("record $fields from $db $where");

  }


?>