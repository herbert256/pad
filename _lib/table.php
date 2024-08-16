<?php
 
 
  function padTable ( $table, $unionBuild = 0 ) {

    global $pad;

    $parms = padTableGetDB ($table);

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
    
    $start = padTableStart ( $all, $distinct, $distinctrow);
    $group = padTableGroup ( $group, $rollup );
    $limit = padTableLimit ( $rows, $page );
    $where = padTableWhere ( $where, $fields, $table, $keys, $db );
    $join  = padTableJoin  ( $join, $fields ); 
    $order = padTableOrder ( $order, $join, $keys );
    $union = padTableUnion ( $union );

    if ($unionBuild) 
      return "union select $start $fields from $db $join $where $group $having $union";
    else
      return db ("$type $start $fields from $db $join $where $group $having $union $order $limit");
        
  }


  function padTableStart ( $all,  $distinct, $distinctrow ) {

    if     ($all)         return 'all';
    elseif ($distinct)    return 'distinct';
    elseif ($distinctrow) return 'distinctrows';
    else                  return '';

  }


  function padTableGroup ( $group, $rollup ) {

    if ($group)
      $group = "group by $group"; 

    if ($rollup) 
      $group .= ' with rollup';

    return $group;

  }


  function padTableOrder ( $order, $joinSQL, $keys ) {

   if ($order)
      return 'order by ' . $order;
    elseif ( !$joinSQL and $keys )
      return 'order by ' . $keys;

  }


  function padTableLimit ( $rows, $page ) {

     $limit = '';

    if ( ! isset($padDone['page']) or ! isset($padDone['rows']))

      if ($page or $rows) {

        if (!$rows) $rows = 10;
        if (!$page) $padPage = 1;
 
        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";          

        padDone ('page', TRUE);
        padDone ('rows', TRUE);

      }

      return $limit;

    }

  function padTableWhere ( $where, &$fields, $table, $keys, $db ) {

    global $padRelations;

    if ($where)
      $where = 'where (' . $where . ')';

    if ( isset ( $padRelations [$table] ) )
      foreach ( $padRelations [$table] as $key => $val)
        padTableGo ( $key, $key, $val, $where );
 
    foreach ( $padRelations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
        if ( $key2 == $table ) 
          padTableGo ( $table, $key, $val, $where );

    $parts = padExplode ($keys, ',');
    foreach($parts as $key)
      if ( padFieldCheck  ($key) )
        padTableWhereAdd ($where, $key, padFieldValue ($key) ); 

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      padTableAddFields ($fields, $db, $work);
    }

    return $where;

  }


  function padTableJoin ( $join, &$fields ) {

    $joinSQL = '';

    if ( ! is_array($join) and $join )
      $joinSQL = ' join ' . $join . ' '; 

    if ( is_array($join) and count($join) ) {

      if ( ! is_array($join[array_key_first($join)]))
        $join = [ 0 => $join];
      
      foreach ($join as $key => $value) {

        foreach ($value as $xtype => $table)
          break;
      
        $joinTable = padTableGetDB ( $table ) ;
        padTableAddFields ($fields, $joinTable ['db'] , $joinTable ['fields'] );
        $joinSQL .= ' ' . $xtype .  ' join ' . $joinTable ['db'] . ' ';
      
        if ( isset($value ['key']) ) {
          $joinSQL .= ' on ';
          $joinSQL .= padTableJoinAdd ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
        }
      
      }
    
    }

    return $joinSQL;

  }


  function padTableJoinAdd ($keys1, $db, $keys2) {

    $where = '';
    
    $values1 = padExplode ($keys1, ',');
    $values2 = padExplode ($keys2, ',');

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';
 
      $where .= padTableField($v) . ' = `' . $db . '`.' . padTableField($values2[$k]);
 
    }

    return $where;
    
  }


  function padTableUnion ( $union) {

    $unionSQL = '';

    if ( is_array($union) )
      $unionQ = $union;
    else {
      $unionQ = array();
      if ($union)
        $unionQ [] = $union;
    }
    
    foreach ($unionQ as $key)
      $unionSQL .= ' ' . padTable ($key, 1);
  
    return $unionSQL;

  }


  function padTableGo ( $table, $key, $val, &$where ) {

    global $pad, $padTableTag, $padData, $padKey;

    $relation = padTableGetDB ($table);

    if ( ! count ( $relation ) )
      return;

    $parts1 = padExplode ( $relation ['key'], ',');
    $parts2 = ( isset($val['key']) ) ? padExplode($val ['key'], ',') : padExplode($relation ['key'], ',');
       
    for ( $i=$pad-1; $i; $i-- )
      if ( $padTableTag[$i] == $key )
        foreach ( $parts2 as $i2 => $key )
          if (  padFieldCheck($key) )
            padTableWhereAdd ($where, $parts1[$i2], padFieldValue($key) );            
  
  }


  function padTableWhereAdd (&$where, $field, $value) {

    $add = padTableField ($field) . ' = ' . "'";

    if ( strpos ( $where, $add ) !== FALSE )
      return;

   if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $add . padEscape ($value) . "'";

  }


  function padTableField ($field) {

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }

  
  function padTableAddFields (&$result, $table, $fields) {
  
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
  

  function padTableGetDB ($table) {
    
    global $padTables;

    if ( ! isset ( $padTables [$table] ) )
      return [ 'db' => $table ];
    
    $parms = $padTables [$table];

    if ( isset($parms['base']) and isset($padTables [$parms['base']]) ) 
      foreach($padTables [$parms['base']] as $key => $value)
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
    

  function padTableGetInfo () {
    
    global $pad, $padTable, $padTables, $padRelations;

    foreach ($padTable [$pad] as $table => $value)

      if ( isset ($padRelations [$table]) )

        foreach ( $padRelations [$table] as $rel => $val)

          if ( ! padTableChk ( $rel ) ) {
         
            $relation = padTableGetDB ($rel);
            $where    = $relation ['where'] ?? '';
            $parts1   = padExplode ($relation ['key'], ',');
            $parts2   = ( isset($val['key']) ) ? $val['key'] : $relation ['key'];
            $parts2   = padExplode ($parts2, ',');
            
            foreach($parts2 as $i => $fld) {
        
              if ( ! isset ( $fld, $padTable [$pad] [$table] ) )
                continue 2;

              padTableWhereAdd ($where, $parts1[$i], $padTable [$pad] [$table] [$fld] ?? '');
               
            }

            if ( $where ) {
              $padTable [$pad] [$rel] = padTableGet ($relation, $where);
              return TRUE;
            }

          }
        
    while ( padTableGetInfoGo () );

    return FALSE;

  }
    
    
  function padTableGetInfoGo () {
  
    global $padTables, $pad, $padTable;

    foreach ($padTables as $key => $val)

      if ( ! padTableChk ($key) and ! isset( $val['virtual'] ) ) {

        $relation = padTableGetDB ($key);

        $where = '';

        foreach ( padExplode ($relation['key']??'', ',') as $fld)
          if ( ! padFieldCheck ($fld) )
            continue 2;
          else
            padTableWhereAdd ( $where, $fld, padFieldValue ($fld) );

        if ( $where ) {
          $padTable [$pad] [$key] = padTableGet ($relation, $where);
          while ( padTableGetInfo () ) ;
          return TRUE;
        }

      }
      
    return FALSE;

  }

  
  function padTableChk ($table) {
    
    global $pad, $padTable;

    for ( $i=$pad; $i; $i--)
      if ( isset ( $padTable [$i] [$table] ) )
        return TRUE;
  
    return FALSE;
  
  }


  function padTableGet ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';
    
    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      padTableAddFields ($fields, $db, $work);
    }

    return db ("record $fields from $db $where");
     
  }


?>