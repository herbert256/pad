<?php

  function padTable ( $table ) {

    return 'ToDo';

    $type = padTagParm ( 'type', 'array' );

    $sql = padTableData ($table);

    return db ("$type $sql");        

  }

  function padTableUnion ( $table ) {
 
    return 'ToDo';

    $union = padTagParm ( 'union', '' );
 
    $sql = padTableData ($table);
    foreach ( padArray($union) as $key )
      $sql .= ' union ' . padTableData ($key, 1);

    return db ("union select $sql");

  }

  function padTableData ($table, $union='') {

    global $padData, $padKey, $padRelations, $padTables;

    $parms = padTableDb ($table);

    $db          = padTagParm ( 'db',          ''      );
    $all         = padTagParm ( 'all',         0       );
    $distinct    = padTagParm ( 'distinct',    0       );
    $distinctrow = padTagParm ( 'distinctrow', 0       );
    $keys        = padTagParm ( 'key',         ''      );
    $fields      = padTagParm ( 'fields',      '*'     );
    $where       = padTagParm ( 'where',       ''      );
    $group       = padTagParm ( 'group',       ''      );
    $rollup      = padTagParm ( 'rollup',      0       );
    $having      = padTagParm ( 'having',     ''       );
    $join        = padTagParm ( 'join',       []       );
    $order       = padTagParm ( 'order',      ''       );
    $page        = padTagParm ( 'page',       0        );
    $rows        = padTagParm ( 'rows',       0        );
    
    if     ($all)         $start = 'all';
    elseif ($distinct)    $start = 'distinct';
    elseif ($distinctrow) $start = 'distinctrows';
    else                  $start = '';
    
    if ($rollup) 
      $group .= ' with rollup';

    $limit = '';

    if ($page or $rows) {

      if (!$rows) $rows = 10;
      if (!$page) $page = 1;

      $offset = ($page-1) * $rows;
      $limit = "limit $offset, $rows";          

    }

    if ($where)
      $where = 'where (' . $where . ')';

    $hit1 = $hit2 = FALSE;

    if ( isset ( $padRelations [$table] ) )
      foreach ( $padRelations [$table] as $key => $val) {
        
        $relation = padTableDb ($key);

        $first = $relation ['key'];

        if ( isset($val['key']) )
          $second = $val ['key'];
        else
          $second = $relation ['key'];
        
        for ( $i=$pad-1; $i; $i--)
          if ( $pad [$i] ==  $key)
            padTableKeysLevel($first, $second, $padData [$i] [$padKey[$i]], $where, $hit1);

      }

    foreach ( $padRelations as $key => $val1)
      foreach ( $val1 as $key2 => $val)
 
        if ( $key2 == $table ) {
          
          $relation = padTableDb ($table);

          $first  = $relation ['key'];
          $second = ( isset($val['key']) ) ? $val ['key'] : $relation ['key'];

          for ( $i=$p; $i; $i--)
            if ( $pad [$i] ==  $key)
              padTableKeysLevel($first, $second, $pData [$i] [$pKey[$i]], $where, $hit2);
  
        }

    if ( !$hit1 and !$hit2 and $keys ) {
      padTableKeysGlobal ($keys, $keys_out, $values_out);
      if ($values_out)
        padbuildWhere ($where, $keys_out, $values_out);
    }

    if ( is_array($fields) ) {
      $work = $fields;
      $fields = '';
      padAddFields ($fields, $db, $work);
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
        $joinTable = padTableDb ( $table ) ;
        padAddFields ($fields, $joinTable ['db'] , $joinTable ['fields'] );
        $joinSQL .= ' ' . $xtype .  ' join ' . $joinTable ['db'] . ' ';
        if ( isset($value ['key']) ) {
          $joinSQL .= ' on ';
          $joinSQL .= padbuildJoinWhere ($value ['key'], $joinTable ['db'], $joinTable ['key']) . ' ';
        }
      }
    }
    
    if     ($order)              $order = 'order by ' . $order;
    elseif (!$joinSQL and $keys) $order = 'order by ' . $keys;

    if ($union)
      $order = $limit = '';

    return "$start $fields from $db $joinSQL $where $group $having $union $order $limit";

  }


  function padTableField ($field) {

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  '`' . $parts[0] . '`.`' . $parts[1] . '`';
    else
      return  '`' . $parts[0] . '`';

  }


  function padbuildJoinWhere ($keys1, $db, $keys2) {

    $where = '';
    
    $values1 = padExplode ($keys1, ',');
    $values2 = padExplode ($keys2, ',');

    if ( count($values1) <> count($values2) )
      padError ("Count keys/values does not match");

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';
 
      $where .= padTableField($v) . ' = `' . $db . '`.' . padTableField($values2[$k]);
 
    }

    return $where;
    
  }

  
  function padAddFields (&$result, $table, $fields) {
  
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
  
  function padTableDb ($table) {
    
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
  


  function padbuildWhere (&$where, $keys, $values) {

    global $keys_parts, $values_parts;
    
    $keys_parts   = padExplode ($keys,   ',');
    $values_parts = padExplode ($values, ',');

    if ( count($keys_parts) <> count($values_parts) )
      padError ("Count keys does not match count values");

    foreach ($keys_parts as $k => $v) {

      if ($where)
        $where .= ' and ';
      else
        $where = 'where ';
      
      $where .= $v . ' = ' . "'" . padEscape ($values_parts[$k]) . "'";

    }

  }




  function padTableKeysLevel($keys1, $keys2, $source, &$where, &$hit) {

    $hit = FALSE;
    
    $parts1 = pExplode ($keys1, ',');
    $parts2 = pExplode ($keys2, ',');
    
    if ( count($parts1) <> count($parts2) )
      padError ("Keys count does not match: $keys1 / $keys2");
      
    foreach($parts2 as $i => $key) {

      if ( array_key_exists($key, $source) ) {

        if ($where)
          $where .= ' and ';
        else
          $where = 'where ';
  
        $where .= $parts1[$i] . ' = ' . "'" . padEscape ($source[$key]??'') . "'";
        
        $hit = TRUE;

      }
        
    }
    
    return $hit;

  }
  
  function padTableKeysGlobal ($keys, &$keys_out, &$values_out) {
    
    $keys_out = $values_out = '';

    $parts = padExplode ($keys, ',');

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
  
  function  padTableInfo () {
    
    global $padTables, $p, $padlvl, $padRelations;

    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;

      foreach ($padlvl [$p] as $table => $value) {

        if ( isset ($padRelations [$table]) ) {

          foreach ( $padRelations [$table] as $rel => $val) {
  
            if ( ! padCheck ( $rel ) ) {
           
              $relation = padTableDb ($rel);
  
              $first = $second = $relation ['key'];
              if ( isset($val['key']) )
                $second  = $val['key'];
  
              $where  = $relation ['where'] ?? '';
  
              $parts1 = pExplode ($first, ',');
              $parts2 = pExplode ($second, ',');
              
              foreach($parts2 as $i => $fld) {
          
                if ( ! isset ( $fld, $padlvl [$p] [$table] ) )
                  continue 2;
 
                padWhere ($where, $parts1[$i], $padlvl [$p] [$table] [$fld] ?? '');
                 
              }
  
              if ( ! $where )
                continue;
  
              $go = TRUE;

              $padlvl [$p] [$rel] = padTableRecord ($relation, $where);
  
            }

          }
          
        }
        
      }
      
    }
    
  }
  

  function padCheck ($table) {
    
    global $p, $padlvl;

    for ( $i=$p; $i; $i--)
      if ( isset ( $padlvl [$i] [$table] ) )
        return TRUE;
  
    return FALSE;
  
  }
  
  function padTableMain () {
    
    global $padTables, $p, $padlvl, $padRelations;

    foreach ($padRelations as $key => $val)
      foreach ($padRelations[$key] as $key2 => $val2)
        if ( ! isset($padTables [$key2] ) ) {
          $padTables [$key2] = $padTables [$padRelations[$key] [$key2] ['table']];
          $padTables [$key2] ['virtual'] = TRUE;
        }    $go = TRUE;
    
    while ($go) {
  
      $go = FALSE;
      
      foreach ($padTables as $key => $val) {
 
        if ( ! padCheck ($key) and ! isset( $val['virtual'] ) ) {

          $relation = padTableDb ($key);
  
          $where = '';
  
          foreach ( pExplode ($relation['key']??'', ',') as $fld)
            if ( ! pField_check($fld) )
              continue 2;
            else
              padWhere ( $where, $fld, pField_value($fld) );
  
          if ( $where ) {
            $x = $relation['db'];
            $padlvl [$p] [$key] = padTableRecord ($relation, $where);
            padTableInfo ();
            $go = TRUE;
          }
  
        }
  
      }
      
    }
      
  }



  function padWhere (&$where, $field, $value) {

    if ($where)
      $where .= ' and ';
    else
      $where = 'where ';

    $where .= $field . ' = ' . "'" . padescape ($value) . "'";

  }
  
  
  function padTableRecord ($relation, $where) {

    $db = $relation ['db'];

    $fields = $relation ['fields'] ?? '*';
    
    if ( is_array($fields) ) {
      $work   = $fields;
      $fields = '';
      padAddFields ($fields, $db, $work);
    }

    $return = db ("record $fields from $db $where");
    
    if ($return === FALSE or $return === NULL)
      return [];
      
    return $return;
      
  }


  
?>