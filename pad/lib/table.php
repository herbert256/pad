<?php


  function pDb_get_data ($table, $page=0, $rows=0, $unionBuild=0) {

    global $pad, $pData, $pPrms_tag, $pKey, $pDb_relations, $pDb_tables, $pDb, $pDone;

    $parms = pDb_get_db ($table);

    $db          = $pPrms_tag ['db']          ?? $parms ['db']          ?? '';
    $all         = $pPrms_tag ['all']         ?? $parms ['all']         ?? 0;
    $distinct    = $pPrms_tag ['distinct']    ?? $parms ['distinct']    ?? 0;
    $distinctrow = $pPrms_tag ['distinctrow'] ?? $parms ['distinctrow'] ?? 0;
    $keys        = $pPrms_tag ['key']         ?? $parms ['key']         ?? '';
    $fields      = $pPrms_tag ['fields']      ?? $parms ['fields']      ?? '*';
    $type        = $pPrms_tag ['type']        ?? $parms ['type']        ?? 'array';
    $where       = $pPrms_tag ['where']       ?? $parms ['where']       ?? '';
    $group       = $pPrms_tag ['group']       ?? $parms ['group']       ?? '';
    $rollup      = $pPrms_tag ['rollup']      ?? $parms ['rollup']      ?? 0;
    $having      = $pPrms_tag ['having']      ?? $parms ['having']      ?? '';
    $join        = $pPrms_tag ['join']        ?? $parms ['join']        ?? [];
    $union       = $pPrms_tag ['union']       ?? $parms ['union']       ?? '';
    $order       = $pPrms_tag ['order']       ?? $parms ['order']       ?? '';
    $page        = $pPrms_tag ['page']        ?? $parms ['page']        ?? $page;
    $rows        = $pPrms_tag ['rows']        ?? $parms ['rows']        ?? $rows;
    
    $start = '';

    if ($all)         $start = 'all';
    if ($distinct)    $start = 'distinct';
    if ($distinctrow) $start = 'distinctrows';

    if ($rollup) 
      $group .= ' with rollup';

    $limit = '';

    if ( ! isset($pDone [$pad]['page']) or ! isset($pDone [$pad]['rows']))

      if ($page or $rows) {

        if (!$rows) $rows = 10;
        if (!$page) $page = 1;
 
        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";          

        pDone ('page', TRUE);
        pDone ( 'rows', TRUE);

      }

    if ($where)
      $where = 'where (' . $where . ')';

    $hit1 = $hit2 = FALSE;

    if ( isset ( $pDb_relations [$table] ) )
      foreach ( $pDb_relations [$table] as $key => $val) {
        
        $relation = pDb_get_db ($key);

        $first = $relation ['key'];

        if ( isset($val['key']) )
          $second = $val ['key'];
        else
          $second = $relation ['key'];
        
        for ( $i=$pad-1; $i; $i--)
          if ( $pDb [$i] ==  $key)
            pDb_get_keys_level ($first, $second, $pData [$i] [$pKey[$i]], $where, $hit1);

      }

    foreach ( $pDb_relations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
 
        if ( $key2 == $table ) {
          
          $relation = pDb_get_db ($table);

          $first  = $relation ['key'];
          $second = ( isset($val['key']) ) ? $val ['key'] : $relation ['key'];

          for ( $i=$pad; $i; $i--)
            if ( $pDb [$i] ==  $key)
              pDb_get_keys_level ($first, $second, $pData [$i] [$pKey[$i]], $where, $hit2);
  
        }

    if ( !$hit1 and !$hit2 and $keys ) {
      pDb_get_keys_global ($keys, $keys_out, $values_out);
      if ($values_out)
        pDb_build_where ($where, $keys_out, $values_out);
    }

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      pDb_add_fields ($fields, $db, $work);
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
        $joinTable = pDb_get_db ( $table ) ;
        pDb_add_fields ($fields, $joinTable ['db'] , $joinTable ['fields'] );
        $joinSQL .= ' ' . $xtype .  ' join ' . $joinTable ['db'] . ' ';
        if ( isset($value ['key']) ) {
          $joinSQL .= ' on ';
          $joinSQL .= pDb_build_join_where ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
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
      $unionSQL .= ' ' . pDb_get_data ($key, 0, 0, 1);

    if ($unionBuild)
      $order = $limit = '';

    $sql = "$start $fields from $db $joinSQL $where $group $having $unionSQL $order $limit";

    if ($unionBuild) 
      return "union select $sql";
    else
      return db ("$type $sql");
        
  }


  function pDb_field ($field) {

    $parts = pExplode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }


  function pDb_build_join_where ($keys1, $db, $keys2) {

    $where = '';
    
    $values1 = pExplode ($keys1, ',');
    $values2 = pExplode ($keys2, ',');

    if ( count($values1) <> count($values2) )
      pError ("Count keys/values does not match");

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';
 
      $where .= pDb_field($v) . ' = `' . $db . '`.' . pDb_field($values2[$k]);
 
    }

    return $where;
    
  }

  
  function pDb_add_fields (&$result, $table, $fields) {
  
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
  
  function pDb_get_db ($table) {
    
    global $pDb_tables;

    $parms = $pDb_tables [$table];

    if (isset($parms['base']))
      foreach($pDb_tables [$parms['base']] as $key => $value)
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
  


  function pDb_build_where (&$where, $keys, $values) {

    global $keys_parts, $values_parts;
    
    $keys_parts   = pExplode ($keys,   ',');
    $values_parts = pExplode ($values, ',');

    if ( count($keys_parts) <> count($values_parts) )
      pError ("Count keys does not match count values");

    foreach ($keys_parts as $k => $v) {

      if ($where)
        $where .= ' and ';
      else
        $where = 'where ';
      
      $where .= $v . ' = ' . "'" . pDb_escape ($values_parts[$k]) . "'";

    }

  }




  function pDb_get_keys_level ($keys1, $keys2, $source, &$where, &$hit) {

    $hit = FALSE;
    
    $parts1 = pExplode ($keys1, ',');
    $parts2 = pExplode ($keys2, ',');
    
    if ( count($parts1) <> count($parts2) )
      pError ("Keys count does not match: $keys1 / $keys2");
      
    foreach($parts2 as $i => $key) {

      if ( array_key_exists($key, $source) ) {

        if ($where)
          $where .= ' and ';
        else
          $where = 'where ';
  
        $where .= $parts1[$i] . ' = ' . "'" . pDb_escape ($source[$key]??'') . "'";
        
        $hit = TRUE;

      }
        
    }
    
    return $hit;

  }
  
  function pDb_get_keys_global ($keys, &$keys_out, &$values_out) {
    
    $keys_out = $values_out = '';

    $parts = pExplode ($keys, ',');

    foreach($parts as $key) {

      if ( pField_check ($key) ) {

        if ($values_out) {
          $keys_out .= ',';
          $values_out .= ',';
        }
  
        $keys_out   .= $key;
        $values_out .= pField_value ($key);

      }

    }

  }
  
  function pDb_get_info () {
    
    global $pDb_tables, $pad, $pDb_lvl, $pDb_relations;

    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;

      foreach ($pDb_lvl [$pad] as $table => $value) {

        if ( isset ($pDb_relations [$table]) ) {

          foreach ( $pDb_relations [$table] as $rel => $val) {
  
            if ( ! pDb_chk ( $rel ) ) {
           
              $relation = pDb_get_db ($rel);
  
              $first = $second = $relation ['key'];
              if ( isset($val['key']) )
                $second  = $val['key'];
  
              $where  = $relation ['where'] ?? '';
  
              $parts1 = pExplode ($first, ',');
              $parts2 = pExplode ($second, ',');
              
              foreach($parts2 as $i => $fld) {
          
                if ( ! isset ( $fld, $pDb_lvl [$pad] [$table] ) )
                  continue 2;
 
                pDb_where ($where, $parts1[$i], $pDb_lvl [$pad] [$table] [$fld] ?? '');
                 
              }
  
              if ( ! $where )
                continue;
  
              $go = TRUE;

              $pDb_lvl [$pad] [$rel] = pDb_get ($relation, $where);
  
            }

          }
          
        }
        
      }
      
    }
    
  }
  

  function pDb_chk ($table) {
    
    global $pad, $pDb_lvl;

    for ( $i=$pad; $i; $i--)
      if ( isset ( $pDb_lvl [$i] [$table] ) )
        return TRUE;
  
    return FALSE;
  
  }
  
  function pDb_get_main () {
    
    global $pDb_tables, $pad, $pDb_lvl, $pDb_relations;

    foreach ($pDb_relations as $key => $val)
      foreach ($pDb_relations[$key] as $key2 => $val2)
        if ( ! isset($pDb_tables [$key2] ) ) {
          $pDb_tables [$key2] = $pDb_tables [$pDb_relations[$key] [$key2] ['table']];
          $pDb_tables [$key2] ['virtual'] = TRUE;
        }    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;
      
      foreach ($pDb_tables as $key => $val) {
 
        if ( ! pDb_chk ($key) and ! isset( $val['virtual'] ) ) {

          $relation = pDb_get_db ($key);
  
          $where = '';
  
          foreach ( pExplode ($relation['key']??'', ',') as $fld)
            if ( ! pField_check($fld) )
              continue 2;
            else
              pDb_where ( $where, $fld, pField_value($fld) );
  
          if ( $where ) {
            $x = $relation['db'];
            $pDb_lvl [$pad] [$key] = pDb_get ($relation, $where);
            pDb_get_info ();
            $go = TRUE;
          }
  
        }
  
      }
      
    }
      
  }



  function pDb_where (&$where, $field, $value) {

    if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $field . ' = ' . "'" . pDb_escape ($value) . "'";

  }
  
  
  function pDb_get ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';
    
    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      pDb_add_fields ($fields, $db, $work);
    }

    $return = db ("record $fields from $db $where");
    
    if ($return === FALSE or $return === NULL)
      return [];
      
    return $return;
      
  }


  
?>