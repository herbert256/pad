<?php


  function pad_db_get_data ($table, $page=0, $rows=0, $unionBuild=0) {

    global $pad_data, $pad, $pad_prms_tag, $pad_key, $pad_db_relations, $pad_db_tables, $pad_db, $pad_done;

    $parms = pad_db_get_db ($table);

    $db          = $pad_prms_tag ['db']          ?? $parms ['db']          ?? '';
    $all         = $pad_prms_tag ['all']         ?? $parms ['all']         ?? 0;
    $distinct    = $pad_prms_tag ['distinct']    ?? $parms ['distinct']    ?? 0;
    $distinctrow = $pad_prms_tag ['distinctrow'] ?? $parms ['distinctrow'] ?? 0;
    $keys        = $pad_prms_tag ['key']         ?? $parms ['key']         ?? '';
    $fields      = $pad_prms_tag ['fields']      ?? $parms ['fields']      ?? '*';
    $type        = $pad_prms_tag ['type']        ?? $parms ['type']        ?? 'array';
    $where       = $pad_prms_tag ['where']       ?? $parms ['where']       ?? '';
    $group       = $pad_prms_tag ['group']       ?? $parms ['group']       ?? '';
    $rollup      = $pad_prms_tag ['rollup']      ?? $parms ['rollup']      ?? 0;
    $having      = $pad_prms_tag ['having']      ?? $parms ['having']      ?? '';
    $join        = $pad_prms_tag ['join']        ?? $parms ['join']        ?? [];
    $union       = $pad_prms_tag ['union']       ?? $parms ['union']       ?? '';
    $order       = $pad_prms_tag ['order']       ?? $parms ['order']       ?? '';
    $page        = $pad_prms_tag ['page']        ?? $parms ['page']        ?? $page;
    $rows        = $pad_prms_tag ['rows']        ?? $parms ['rows']        ?? $rows;
    
    $start = '';

    if ($all)         $start = 'all';
    if ($distinct)    $start = 'distinct';
    if ($distinctrow) $start = 'distinctrows';

    if ($rollup) 
      $group .= ' with rollup';

    $limit = '';

    if ( ! isset($pad_done['page']) or ! isset($pad_done['rows']))

      if ($page or $rows) {

        if (!$rows) $rows = 10;
        if (!$page) $page = 1;
 
        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";          

        pad_set_arr_var ('done', 'page', TRUE);
        pad_set_arr_var ('done', 'rows', TRUE);

      }

    if ($where)
      $where = 'where (' . $where . ')';

    $hit1 = $hit2 = FALSE;

    if ( isset ( $pad_db_relations [$table] ) )
      foreach ( $pad_db_relations [$table] as $key => $val) {
        
        $relation = pad_db_get_db ($key);

        $first = $relation ['key'];

        if ( isset($val['key']) )
          $second = $val ['key'];
        else
          $second = $relation ['key'];
        
        for ( $i=$pad-1; $i; $i--)
          if ( $pad_db [$i] ==  $key)
            pad_db_get_keys_level ($first, $second, $pad_data [$i] [$pad_key[$i]], $where, $hit1);

      }

    foreach ( $pad_db_relations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
 
        if ( $key2 == $table ) {
          
          $relation = pad_db_get_db ($table);

          $first  = $relation ['key'];
          $second = ( isset($val['key']) ) ? $val ['key'] : $relation ['key'];

          for ( $i=$pad; $i; $i--)
            if ( $pad_db [$i] ==  $key)
              pad_db_get_keys_level ($first, $second, $pad_data [$i] [$pad_key[$i]], $where, $hit2);
  
        }

    if ( !$hit1 and !$hit2 and $keys ) {
      pad_db_get_keys_global ($keys, $keys_out, $values_out);
      if ($values_out)
        pad_db_build_where ($where, $keys_out, $values_out);
    }

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      pad_db_add_fields ($fields, $db, $work);
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
        $joinTable = pad_db_get_db ( $table ) ;
        pad_db_add_fields ($fields, $joinTable ['db'] , $joinTable ['fields'] );
        $joinSQL .= ' ' . $xtype .  ' join ' . $joinTable ['db'] . ' ';
        if ( isset($value ['key']) ) {
          $joinSQL .= ' on ';
          $joinSQL .= pad_db_build_join_where ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
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
      $unionSQL .= ' ' . pad_db_get_data ($key, 0, 0, 1);

    if ($unionBuild)
      $order = $limit = '';

    $sql = "$start $fields from $db $joinSQL $where $group $having $unionSQL $order $limit";

    if ($unionBuild) 
      return "union select $sql";
    else
      return db ("$type $sql");
        
  }


  function pad_db_field ($field) {

    $parts = pad_explode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }


  function pad_db_build_join_where ($keys1, $db, $keys2) {

    $where = '';
    
    $values1 = pad_explode ($keys1, ',');
    $values2 = pad_explode ($keys2, ',');

    if ( count($values1) <> count($values2) )
      pad_error ("Count keys/values does not match");

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';
 
      $where .= pad_db_field($v) . ' = `' . $db . '`.' . pad_db_field($values2[$k]);
 
    }

    return $where;
    
  }

  
  function pad_db_add_fields (&$result, $table, $fields) {
  
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
  
  function pad_db_get_db ($table) {
    
    global $pad_db_tables;

    $parms = $pad_db_tables [$table];

    if (isset($parms['base']))
      foreach($pad_db_tables [$parms['base']] as $key => $value)
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
  


  function pad_db_build_where (&$where, $keys, $values) {

    global $keys_parts, $values_parts;
    
    $keys_parts   = pad_explode ($keys,   ',');
    $values_parts = pad_explode ($values, ',');

    if ( count($keys_parts) <> count($values_parts) )
      pad_error ("Count keys does not match count values");

    foreach ($keys_parts as $k => $v) {

      if ($where)
        $where .= ' and ';
      else
        $where = 'where ';
      
      $where .= $v . ' = ' . "'" . pad_db_escape ($values_parts[$k]) . "'";

    }

  }




  function pad_db_get_keys_level ($keys1, $keys2, $source, &$where, &$hit) {

    $hit = FALSE;
    
    $parts1 = pad_explode ($keys1, ',');
    $parts2 = pad_explode ($keys2, ',');
    
    if ( count($parts1) <> count($parts2) )
      pad_error ("Keys count does not match: $keys1 / $keys2");
      
    foreach($parts2 as $i => $key) {

      if ( array_key_exists($key, $source) ) {

        if ($where)
          $where .= ' and ';
        else
          $where = 'where ';
  
        $where .= $parts1[$i] . ' = ' . "'" . pad_db_escape ($source[$key]??'') . "'";
        
        $hit = TRUE;

      }
        
    }
    
    return $hit;

  }
  
  function pad_db_get_keys_global ($keys, &$keys_out, &$values_out) {
    
    $keys_out = $values_out = '';

    $parts = pad_explode ($keys, ',');

    foreach($parts as $key) {

      if ( pad_field_check ($key) ) {

        if ($values_out) {
          $keys_out .= ',';
          $values_out .= ',';
        }
  
        $keys_out   .= $key;
        $values_out .= pad_field_value ($key);

      }

    }

  }
  
  function pad_db_get_info () {
    
    global $pad_db_tables, $pad, $pad_db_lvl, $pad_db_relations;

    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;

      foreach ($pad_db_lvl [$pad] as $table => $value) {

        if ( isset ($pad_db_relations [$table]) ) {

          foreach ( $pad_db_relations [$table] as $rel => $val) {
  
            if ( ! pad_db_chk ( $rel ) ) {
           
              $relation = pad_db_get_db ($rel);
  
              $first = $second = $relation ['key'];
              if ( isset($val['key']) )
                $second  = $val['key'];
  
              $where  = $relation ['where'] ?? '';
  
              $parts1 = pad_explode ($first, ',');
              $parts2 = pad_explode ($second, ',');
              
              foreach($parts2 as $i => $fld) {
          
                if ( ! isset ( $fld, $pad_db_lvl [$pad] [$table] ) )
                  continue 2;
 
                pad_db_where ($where, $parts1[$i], $pad_db_lvl [$pad] [$table] [$fld] ?? '');
                 
              }
  
              if ( ! $where )
                continue;
  
              $go = TRUE;

              $pad_db_lvl [$pad] [$rel] = pad_db_get ($relation, $where);
  
            }

          }
          
        }
        
      }
      
    }
    
  }
  

  function pad_db_chk ($table) {
    
    global $pad, $pad_db_lvl;

    for ( $i=$pad; $i; $i--)
      if ( isset ( $pad_db_lvl [$i] [$table] ) )
        return TRUE;
  
    return FALSE;
  
  }
  
  function pad_db_get_main () {
    
    global $pad_db_tables, $pad, $pad_db_lvl, $pad_db_relations;

    foreach ($pad_db_relations as $key => $val)
      foreach ($pad_db_relations[$key] as $key2 => $val2)
        if ( ! isset($pad_db_tables [$key2] ) ) {
          $pad_db_tables [$key2] = $pad_db_tables [$pad_db_relations[$key] [$key2] ['table']];
          $pad_db_tables [$key2] ['virtual'] = TRUE;
        }    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;
      
      foreach ($pad_db_tables as $key => $val) {
 
        if ( ! pad_db_chk ($key) and ! isset( $val['virtual'] ) ) {

          $relation = pad_db_get_db ($key);
  
          $where = '';
  
          foreach ( pad_explode ($relation['key']??'', ',') as $fld)
            if ( ! pad_field_check($fld) )
              continue 2;
            else
              pad_db_where ( $where, $fld, pad_field_value($fld) );
  
          if ( $where ) {
            $x = $relation['db'];
            $pad_db_lvl [$pad] [$key] = pad_db_get ($relation, $where);
            pad_db_get_info ();
            $go = TRUE;
          }
  
        }
  
      }
      
    }
      
  }



  function pad_db_where (&$where, $field, $value) {

    if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $field . ' = ' . "'" . pad_db_escape ($value) . "'";

  }
  
  
  function pad_db_get ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';
    
    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      pad_db_add_fields ($fields, $db, $work);
    }

    $return = db ("record $fields from $db $where");
    
    if ($return === FALSE or $return === NULL)
      return [];
      
    return $return;
      
  }


  
?>