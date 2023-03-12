<?php

  function padTableGetData ($table, $page=0, $rows=0, $unionBuild=0) {

    global $padData, $pad, $padPrm, $padKey, $padRelations, $padTables, $padTable, $padDone;

    $parms = padTableGetDB ($table);

    $db          = $padPrm [$pad] ['db']          ?? $parms ['db']          ?? '';
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
    $page        = $padPrm [$pad] ['page']        ?? $parms ['page']        ?? $page;
    $rows        = $padPrm [$pad] ['rows']        ?? $parms ['rows']        ?? $rows;
    
    $start = '';

    if ($all)         $start = 'all';
    if ($distinct)    $start = 'distinct';
    if ($distinctrow) $start = 'distinctrows';

    if ($rollup) 
      $group .= ' with rollup';

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

    if ($where)
      $where = 'where (' . $where . ')';

    $hit1 = $hit2 = FALSE;

    if ( isset ( $padRelations [$table] ) )
      foreach ( $padRelations [$table] as $key => $val) {
        
        $relation = padTableGetDB ($key);

        $first = $relation ['key'];

        if ( isset($val['key']) )
          $second = $val ['key'];
        else
          $second = $relation ['key'];
        
        for ( $i=$pad-1; $i; $i--)
          if ( $padTable [$i] ==  $key)
            padTableGetKeysLevel ($first, $second, $padData [$i] [$padKey[$i]], $where, $hit1);

      }

    foreach ( $padRelations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
 
        if ( $key2 == $table ) {
          
          $relation = padTableGetDB ($table);

          $first  = $relation ['key'];
          $second = ( isset($val['key']) ) ? $val ['key'] : $relation ['key'];

          for ( $i=$pad; $i; $i--)
            if ( $padTable [$i] ==  $key)
              padTableGetKeysLevel ($first, $second, $padData [$i] [$padKey[$i]], $where, $hit2);
  
        }

    if ( !$hit1 and !$hit2 and $keys ) {
      padTableGetKeysGlobal ($keys, $keys_out, $values_out);
      if ($values_out)
        padTableBuildWhere ($where, $keys_out, $values_out);
    }

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      padTableAddFields ($fields, $db, $work);
    }

    $joinSQL = '';

    if ( ! is_array($join) and $join ) {
      $joinSQL = ' natural join ' . $join . ' '; 
    } 

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
          $joinSQL .= padTableBuildJoinWhere ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
        }
      }
    }
    
    if ($order)
      $order = 'order by ' . $order;
    elseif (!$joinSQL and $keys)
      $order = 'order by ' . $keys;

    $unionSQL = '';

    if ( is_array($union) )
      $unionQ = $union;
    else {
      $unionQ = array();
      if ($union)
        $unionQ [] = $union;
    }
    
    foreach ($unionQ as $key)
      $unionSQL .= ' ' . padTableGetData ($key, 0, 0, 1);

    if ($unionBuild)
      $order = $limit = '';

    $sql = "$start $fields from $db $joinSQL $where $group $having $unionSQL $order $limit";

    if ($unionBuild) 
      return "union select $sql";
    else
      return db ("$type $sql");
        
  }


  function padTableField ($field) {

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }


  function padTableBuildJoinWhere ($keys1, $db, $keys2) {

    $where = '';
    
    $values1 = padExplode ($keys1, ',');
    $values2 = padExplode ($keys2, ',');

    if ( count($values1) <> count($values2) )
      pad_error ("Count keys/values does not match");

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';
 
      $where .= padTableField($v) . ' = `' . $db . '`.' . padTableField($values2[$k]);
 
    }

    return $where;
    
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

    $parms = $padTables [$table];

    if (isset($parms['base']))
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
  


  function padTableBuildWhere (&$where, $keys, $values) {

    global $keys_parts, $values_parts;
    
    $keys_parts   = padExplode ($keys,   ',');
    $values_parts = padExplode ($values, ',');

    if ( count($keys_parts) <> count($values_parts) )
      pad_error ("Count keys does not match count values");

    foreach ($keys_parts as $k => $v) {

      if ($where)
        $where .= ' and ';
      else
        $where = 'where ';
      
      $where .= $v . ' = ' . "'" . padTableescape ($values_parts[$k]) . "'";

    }

  }


  function padTableGetKeysLevel ($keys1, $keys2, $source, &$where, &$hit) {

    $hit = FALSE;
    
    $parts1 = padExplode ($keys1, ',');
    $parts2 = padExplode ($keys2, ',');
    
    if ( count($parts1) <> count($parts2) )
      pad_error ("Keys count does not match: $keys1 / $keys2");
      
    foreach($parts2 as $i => $key) {

      if ( array_key_exists($key, $source) ) {

        if ($where)
          $where .= ' and ';
        else
          $where = 'where ';
  
        $where .= $parts1[$i] . ' = ' . "'" . padTableescape ($source[$key]??'') . "'";
        
        $hit = TRUE;

      }
        
    }
    
    return $hit;

  }
  
  function padTableGetKeysGlobal ($keys, &$keys_out, &$values_out) {
    
    $keys_out = $values_out = '';

    $parts = padExplode ($keys, ',');

    foreach($parts as $key) {

      if ( padFieldCheck  ($key) ) {

        if ($values_out) {
          $keys_out .= ',';
          $values_out .= ',';
        }
  
        $keys_out   .= $key;
        $values_out .= padFieldValue ($key);

      }

    }

  }
  
  function padTableGetInfo () {
    
    global $padTables, $pad, $padTable, $padRelations;

    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;

      foreach ($padTable [$pad] as $table => $value) {

        if ( isset ($padRelations [$table]) ) {

          foreach ( $padRelations [$table] as $rel => $val) {
  
            if ( ! padTableChk ( $rel ) ) {
           
              $relation = padTableGetDB ($rel);
  
              $first = $second = $relation ['key'];
              if ( isset($val['key']) )
                $second  = $val['key'];
  
              $where  = $relation ['where'] ?? '';
  
              $parts1 = padExplode ($first, ',');
              $parts2 = padExplode ($second, ',');
              
              foreach($parts2 as $i => $fld) {
          
                if ( ! isset ( $fld, $padTable [$pad] [$table] ) )
                  continue 2;
 
                padTableWhere ($where, $parts1[$i], $padTable [$pad] [$table] [$fld] ?? '');
                 
              }
  
              if ( ! $where )
                continue;
  
              $go = TRUE;

              $padTable [$pad] [$rel] = padTableGet ($relation, $where);
  
            }

          }
          
        }
        
      }
      
    }
    
  }
  

  function padTableChk ($table) {
    
    global $pad, $padTable;

    for ( $i=$pad; $i; $i--)
      if ( isset ( $padTable [$i] [$table] ) )
        return TRUE;
  
    return FALSE;
  
  }
  
  function padTableGetMain () {
    
    global $padTables, $pad, $padTable, $padRelations;

    foreach ($padRelations as $key => $val)
      foreach ($padRelations[$key] as $key2 => $val2)
        if ( ! isset($padTables [$key2] ) ) {
          $padTables [$key2] = $padTables [$padRelations[$key] [$key2] ['table']];
          $padTables [$key2] ['virtual'] = TRUE;
        }    

    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;
      
      foreach ($padTables as $key => $val) {
 
        if ( ! padTableChk ($key) and ! isset( $val['virtual'] ) ) {

          $relation = padTableGetDB ($key);
  
          $where = '';
  
          foreach ( padExplode ($relation['key']??'', ',') as $fld)
            if ( ! padFieldCheck ($fld) )
              continue 2;
            else
              padTableWhere ( $where, $fld, padFieldValue ($fld) );
  
          if ( $where ) {
            $x = $relation['db'];
            $padTable [$pad] [$key] = padTableGet ($relation, $where);
            padTableGetInfo ();
            $go = TRUE;
          }
  
        }
  
      }
      
    }
      
  }



  function padTableWhere (&$where, $field, $value) {

    if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $field . ' = ' . "'" . padEscape ($value) . "'";

  }
  
  
  function padTableGet ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';
    
    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      padTableAddFields ($fields, $db, $work);
    }

    $return = db ("record $fields from $db $where");
    
    if ($return === FALSE or $return === NULL)
      return [];
      
    return $return;
      
  }


  
?>