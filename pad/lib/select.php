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

    global     $padSelStart, $padSelGroup, $padSelLimit, $padSelWhere, $padSelJoin, $padSelOrder, $padSelUnion;

    $parms = padSelectGetDB ($table);

    // A union member is built from its declaration alone: the tag's own options belong to
    // the outer query, and reading them here made a tag-side union='member' find itself in
    // the member's build and recurse without end.

    $prm = ( $unionBuild ) ? [] : ( $padPrm [$pad] ?? [] );

    if ( ! padSelectFragments ( $prm ) )
      return [];

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
    $padSelWhere  = $prm ['where']        ?? $parms ['where']       ?? '';
    $padSelGroup  = $prm ['group']        ?? $parms ['group']       ?? '';
    $rollup       = $prm ['rollup']       ?? $parms ['rollup']      ?? 0;
    $having       = $prm ['having']       ?? $parms ['having']      ?? '';
    $padSelJoin   = $prm ['join']         ?? $parms ['join']        ?? [];
    $padSelUnion  = $prm ['union']        ?? $parms ['union']       ?? '';
    $padSelOrder  = $prm ['order']        ?? $parms ['order']       ?? '';
    $page         = $prm ['page']         ?? $parms ['page']        ?? 0;
    $rows         = $prm ['rows']         ?? $parms ['rows']        ?? 0;
    $htmlAttrJson = $prm ['htmlAttrJson'] ?? $parms ['htmlAttrJson'] ?? 0;

    if ( ! $padHtmlAttrJson and $htmlAttrJson ) {
      $padHtmlAttrJson = $htmlAttrJson;
      $type            = $htmlAttrJson;
    }

    $padSelStart = padSelectStart  ( $all, $distinct, $distinctrow);
    $padSelGroup = padSelectGroup  ( $padSelGroup, $rollup );
    $having      = padSelectHaving ( $having );
    $padSelLimit = padSelectLimit  ( $rows, $page );
    $padSelWhere = padSelectWhere  ( $padSelWhere, $table, $keys );
    $fields      = padSelectFields ( $fields, $db );
    $padSelJoin  = padSelectJoin   ( $padSelJoin, $fields );
    $keys        = padSelectKeys   ( $keys );
    $padSelOrder = padSelectOrder  ( $padSelOrder, $padSelJoin, $keys );

    // The outer query's parts are folded into text before the union members are built:
    // the composition variables are globals - the dump reads them, under pad-prefixed names
    // so a $where or $order of the application is not overwritten - and a member's
    // build writes its own parts into the same names, so a union query composed after it
    // carried the member's where instead of its own.

    $head = "$padSelStart $fields from $db $padSelJoin $padSelWhere $padSelGroup $having";
    $tail = "$padSelOrder $padSelLimit";

    $padSelUnion = padSelectUnion  ( $padSelUnion );

    $_UNION [] = $padSelUnion;

    $base  = "$head $padSelUnion";
    $sql   = "$type $base $tail";
    $padSelUnion = "union select $base";

    $_SELECT [] = $sql;

    if ($unionBuild)
      return $padSelUnion;
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

  // The page is taken by the SQL limit, and the two options are marked 'limit' on this
  // level's book: handling/types/page.php sees the mark and leaves the rows alone. The
  // guard read $padDone ['page'], a key the per-level book never has, so the pager ran
  // again on the rows the limit had already cut - page 2 and on came out empty.

  function padSelectLimit ( $rows, $page ) {

    global $pad, $padDone;

    $limit = '';

    // Numbers, whatever the options held: they are spliced into the statement as written.

    $rows = (int) $rows;
    $page = (int) $page;

    if ( ( $padDone [$pad] ['page'] ?? '' ) !== 'limit' )

      if ($page or $rows) {

        if ($rows < 1) $rows = 10;
        if ($page < 1) $page = 1;

        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";

        padDone ('page', 'limit');
        padDone ('rows', 'limit');

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

    $padSelOuter = '';

    for ( $i=$pad-1; $i; $i-- )
      if ( $padType [$i] == 'select' ) {

        $relation    = $padTag [$i] ;
        $parms       = padSelectGetDB ( $relation ) ;
        $padSelOuter = $relation;

        padSelectWhereRelation ( $where, $table, $relation, $padCurrent [$i] );

        while ( isset ( $parms ['base'] ) ) {
          $relation = $parms ['base'];
          $parms    = padSelectGetDB ( $relation ) ;
          padSelectWhereRelation ( $where, $table, $relation, $padCurrent [$i] );
        }

      }

    // A nested select that ends up with no constraint at all - no where of its own, no
    // key bound, no declared relation to the select it stands in - meets every row
    // against every row, silently. Strict mode names the missing relation.

    global $padCheckSyntax;

    if ( $padCheckSyntax and $padSelOuter and $where == '' )
      padError ( "no relation declared between '$padSelOuter' and '$table' - every row meets every row" );

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

  // The value - a key bound on the tag, often straight from the request, or a field of the
  // outer row - goes into the SQL string literal escaped for SQL, by the application's own
  // connection. It went through padEscape, which turns PAD's delimiters into entities and
  // leaves quotes alone: an apostrophe ended the literal and the rest of the value became
  // part of the condition, while an honest value holding a comma, = or @ was rewritten and
  // never matched.

  function padSelectWhereAdd  (&$where, $field, $value) {

    $add = padSelectField ($field) . ' = ' . "'";

    if ( strpos ( $where, $add ) !== FALSE )
      return;

    if ($where) $where .= ' and ';
    else        $where  = 'where ';

    $where .= $add . padSelectEscape ($value) . "'";

  }

  // The SQL a tag writes itself - where=, having=, order=, group= - is spliced into the
  // statement as SQL, so it has to be the template's own text. Each must be written as a
  // quoted string on the tag: where=$cond took a whole condition from a variable, which
  // request input could fill. A value goes into where= and having= as $name, which this
  // binds as a quoted literal of the application variable - the {$name} form splices text
  // instead and is the template author's SQL. In order= and group= a $name is spliced as
  // it is, so it must hold column names, each with an optional asc or desc. A table's
  // declaration in $padSelect is PHP and is taken as written. FALSE refuses the query.

  function padSelectFragments ( &$prm ) {

    global $pad, $padParms;

    foreach ( [ 'where', 'having', 'order', 'group' ] as $part ) {

      if ( ! isset ( $prm [$part] ) or ! is_string ( $prm [$part] ) )
        continue;

      $org = '';

      // The record keeps the item as written, name=value; the value is what follows the =.

      foreach ( $padParms [$pad] ?? [] as $one )
        if ( ( $one ['padPrmName'] ?? '' ) === $part ) {
          $org = (string) ( $one ['padPrmOrg'] ?? '' );
          $org = trim ( str_contains ( $org, '=' ) ? substr ( $org, strpos ( $org, '=' ) + 1 ) : $org );
        }

      if ( $org !== '' and ! is_numeric ( $org ) and $org [0] != "'" and $org [0] != '"' )
        return padError ( "$part= is SQL the template writes: give it as a quoted string, and put a value in it as \$name" );

      $prm [$part] = padSelectBind ( $prm [$part], in_array ( $part, [ 'order', 'group' ] ) );

      if ( $prm [$part] === FALSE )
        return FALSE;

    }

    return TRUE;

  }

  // Replaces each $name outside a quoted literal with the value of that application
  // variable: a quoted, escaped literal (a number as a number, NULL as NULL), or for a
  // column list the value as it is, which padSelectColumns then has to accept.

  function padSelectBind ( $sql, $columns = FALSE ) {

    $out   = '';
    $quote = '';
    $len   = strlen ( $sql );

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      if ( $quote ) {
        if ( $char == '\\' and $i + 1 < $len ) { $out .= $char . $sql [++$i]; continue; }
        if ( $char == $quote ) $quote = '';
      } elseif ( $char == "'" or $char == '"' or $char == '`' )
        $quote = $char;
      elseif ( $char == '$' and preg_match ( '/\G\$([A-Za-z][A-Za-z0-9_]*)/', $sql, $match, 0, $i ) ) {

        $name = $match [1];

        if ( ! padValidVar ( $name ) or ! array_key_exists ( $name, $GLOBALS ) )
          return padError ( "there is no application variable named \$$name for the select" );

        $value = $GLOBALS [$name];

        if ( $columns and ! padSelectColumns ( (string) $value ) )
          return padError ( "\$$name in order= or group= must hold column names, each with an optional asc or desc" );

        if     ( $columns                ) $out .= (string) $value;
        elseif ( $value === NULL         ) $out .= 'NULL';
        elseif ( is_bool ( $value )      ) $out .= $value ? '1' : '0';
        elseif ( is_int ( $value ) or is_float ( $value ) or preg_match ( '/^-?[0-9]+(\.[0-9]+)?$/', (string) $value ) )
                                           $out .= (string) $value;
        else                               $out .= "'" . padSelectEscape ( $value ) . "'";

        $i += strlen ( $match [0] ) - 1;
        continue;

      }

      $out .= $char;

    }

    return $out;

  }

  function padSelectColumns ( $list ) {

    $name = '(`[^`]+`|[A-Za-z_][A-Za-z0-9_]*)';
    $item = "\\s*($name(\\.$name)?|[0-9]+)(\\s+(asc|desc))?\\s*";

    return (bool) preg_match ( "/^$item(,$item)*$/i", $list );

  }

  function padSelectEscape ( $value ) {

    global $padSqlConnect, $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase;

    if ( ! isset ( $padSqlConnect ) )
      $padSqlConnect = padDbConnect ( $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase );

    return mysqli_real_escape_string ( $padSqlConnect, (string) ( $value ?? '' ) );

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

        if ( isset ( $joinTable ['fields'] ) )
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