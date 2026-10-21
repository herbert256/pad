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
  //
  // Read when a page walks a select table: types/select.php includes this file. The binding
  // of a $name in the template's SQL - padSelectBind, padSelectColumns, padSelectEscape -
  // stays in lib/select.php, as the database tags bind their SQL with it too.

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

    // The mode becomes the statement's command word, so it is one of the shapes db() knows
    // for rows - array, record or field - and a bare htmlAttrJson means array. Any other
    // value went into the SQL as written.

    if ( $htmlAttrJson === TRUE or $htmlAttrJson === 1 or $htmlAttrJson === '1' )
      $htmlAttrJson = 'array';

    if ( $htmlAttrJson and ! in_array ( strtolower ( (string) $htmlAttrJson ), [ 'array', 'record', 'field' ], TRUE ) )
      return padError ( "htmlAttrJson= takes array, record or field, not '" . padMakeSafe ( (string) $htmlAttrJson, 40 ) . "'" );

    if ( ! $padHtmlAttrJson and $htmlAttrJson ) {
      $padHtmlAttrJson = strtolower ( $htmlAttrJson );
      $type            = $padHtmlAttrJson;
    }

    // The mode is the statement's command word, so it must be one of the row shapes db()
    // knows - array, record or field. It was spliced in front of the SQL as written, so
    // {table type=$t} with $t = "array name from customers limit 1 #" put a whole query of
    // the visitor's into the statement; a bad word is refused now, as htmlAttrJson= is.

    $type = strtolower ( (string) $type );

    if ( ! in_array ( $type, [ 'array', 'record', 'field' ], TRUE ) )
      return padError ( "type= takes array, record or field, not '" . padMakeSafe ( $type, 40 ) . "'" );

    $padSelStart = padSelectStart  ( $all, $distinct, $distinctrow);
    $padSelGroup = padSelectGroup  ( $padSelGroup, $rollup );
    $having      = padSelectHaving ( $having );
    $padSelLimit = padSelectLimit  ( $rows, $page );
    $padSelWhere = padSelectWhere  ( $padSelWhere, $table, $keys, isset ( $prm ['where'] ) );
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
    $members     = $padSelUnion;

    $_UNION [] = $padSelUnion;

    $base  = "$head $padSelUnion";
    $sql   = "$type $base $tail";
    $padSelUnion = "union select $base";

    $_SELECT [] = $sql;

    // A page cut by the limit: the statement that counts every row is kept for a {pager},
    // which runs it only when it is asked - lib/pager.php. The rows are counted over the
    // FROM, the joins and the where alone, unless distinct, a group, a having or a union
    // makes the select list decide what a row is: a derived table may not hold two columns
    // of one name, so on MySQL a join of two tables that share one - select * from orders
    // join customers, both with customerNumber - ended the page as soon as it was counted.
    // The fields decide it as well when they start with distinct or hold an aggregate:
    // fields='distinct country' had the pages of every customer, and fields='count(*) as n'
    // a pager over its one row.

    if ( $padSelLimit and ! $unionBuild ) {
      $short = ( in_array ( $padSelStart, [ '', 'ALL' ] ) and ! $padSelGroup and ! $having and trim ( $members ) === ''
                 and ! preg_match ( '/^\s*(all\s+)?distinct(row)?\b/i', (string) $fields )
                 and ! preg_match ( '/\b(count|sum|avg|min|max|total|group_concat|json_arrayagg|json_objectagg|string_agg|bit_and|bit_or|bit_xor|std|stddev|stddev_pop|stddev_samp|variance|var_pop|var_samp)\s*\(/i', (string) $fields ) );
      $count = $short
             ? "select 1 from $db $padSelJoin $padSelWhere"
             : "select $base";
      $GLOBALS ['padPagerCount'] [$pad] = "field count(*) from ( $count ) as padPagerCount";
      $GLOBALS ['padPagerRows']  [$pad] = $rows;
    }

    if ($unionBuild)
      return $padSelUnion;
    else
      return db ( $sql );

  }

  function padSelectKeys ( $keys ) {

    if ( ! $keys )
      return '';

    return implode ( ',', array_map ( 'padSelectField', padExplode ( $keys, ',' ) ) );

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
  //
  // $rows and $page come back as the numbers the limit was made of: the {pager} counts its
  // pages by those rows (lib/pager.php), which may be the table's declared rows= - it took
  // the tag's rows= or 10, and a table declared with 'rows' => 5 had its pager count pages
  // of 10.

  function padSelectLimit ( &$rows, &$page ) {

    global $pad, $padDone;

    $limit = '';

    // Numbers, whatever the options held: they are spliced into the statement as written.

    $rows = (int) $rows;
    $page = (int) $page;

    if ( ( $padDone [$pad] ['page'] ?? '' ) !== 'limit' )

      if ($page or $rows) {

        if ($rows < 1) $rows = 10;
        if ($page < 1) $page = 1;

        // A page from the request past PHP_INT_MAX / rows made the offset a float, written
        // 1.844674407371E+19 - an SQL syntax error a visitor could raise at will. Held there,
        // it is a page past the last, as any page too high is.

        $page   = min ( $page, intdiv ( PHP_INT_MAX, $rows ) );
        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";

        padDone ('page', 'limit');
        padDone ('rows', 'limit');

      }

    return $limit;

  }

  // $tagWhere: the where= was written on the tag, not taken from the declaration.

  function padSelectWhere ( $where, $table, $keys, $tagWhere = FALSE ) {

    global $pad, $padRelations, $padCurrent, $padTag, $padType, $padSetLvl;

    if ($where)
      $where = 'where (' . $where . ')';

    $bound = FALSE;

    foreach ( padExplode ( $keys, ',' ) as $field )
      if ( isset ( $padSetLvl [$pad] [$field] ) ) {
        padSelectWhereAdd ( $where, "$field", $GLOBALS [$field] );
        $bound = TRUE;
      }

    // Each enclosing select is related to this one by the first of this table's names - its
    // own, then each base= it stands on - that has a relation to the outer table or to a
    // table of the outer's base= chain. Only the outer's chain was walked: a view of
    // employees nested in {offices} was related by nothing, and every office listed every
    // row of the view.

    $padSelOuter = '';

    for ( $i=$pad-1; $i; $i-- )
      if ( $padType [$i] == 'select' ) {

        $padSelOuter = $padTag [$i];

        foreach ( padSelectChain ( $table ) as $inner ) {

          $found = FALSE;

          foreach ( padSelectChain ( $padTag [$i] ) as $relation )
            if ( padSelectWhereRelation ( $where, $inner, $relation, $padCurrent [$i] ) )
              $found = TRUE;

          if ( $found ) {
            $bound = TRUE;
            break;
          }

        }

      }

    // A nested select that nothing relates to the select it stands in - no declared
    // relation, no key bound, no where= of its own on the tag - meets every row against
    // every row, silently. Strict mode names the missing relation. A view's declared where
    // is no relation: it holds no value of the outer row, so it filters every office alike.

    global $padCheckSyntax;

    if ( $padCheckSyntax and $padSelOuter and ! $bound and ! $tagWhere )
      padError ( "no relation declared between '$padSelOuter' and '$table' - every row meets every row" );

    return $where;

  }

  // The table and the tables of its base= chain, in that order - a chain that comes back
  // to a table already in it ends there.

  function padSelectChain ( $table ) {

    global $padSelect;

    $chain = [ $table ];

    while ( is_string ( $base = $padSelect [ end ( $chain ) ] ['base'] ?? NULL ) and ! in_array ( $base, $chain, TRUE ) )
      $chain [] = $base;

    return $chain;

  }

  // TRUE when a relation between the two is declared, in either direction.

  function padSelectWhereRelation ( &$where, $table, $relation, $data ) {

    global $padRelations;

    if  ( isset ( $padRelations [$relation] [$table] ) )
      padSelectWhereKeys ( $where, padSelectRelationKeys ( $padRelations [$relation] [$table], $relation, $table ), $data, 0, $relation );
    elseif ( isset ( $padRelations [$table] [$relation] ) )
      padSelectWhereKeys ( $where, padSelectRelationKeys ( $padRelations [$table] [$relation], $table, $relation ), $data, 1, $relation );
    else
      return FALSE;

    return TRUE;

  }

  // A relation $padRelations [first] [second] is written in one of three forms: a field
  // name both tables share, [ field of first => field of second, ... ], or - the form
  // DATABASE.md gives - [ 'key' => 'user_id' ]: the field(s) of the first table that hold
  // the declared key of the second. That last form is made the second here. It was taken
  // for the second form, a field named key, since the relations were rewritten to it: the
  // documented forum_topics/users example ended the request on an undefined array key.

  function padSelectRelationKeys ( $keys, $first, $second ) {

    if ( ! is_array ( $keys ) or array_keys ( $keys ) !== [ 'key' ] )
      return $keys;

    $fields = padExplode ( (string) $keys ['key'], ',' );
    $refers = padExplode ( (string) ( padSelectGetDB ( $second ) ['key'] ?? '' ), ',' );

    if ( ! $refers or count ( $fields ) != count ( $refers ) ) {
      padError ( "the relation of '$first' to '$second' is written [ 'key' => '" . $keys ['key'] . "' ], "
               . "which needs the key of '$second' declared with as many fields" );
      return $keys ['key'];
    }

    return array_combine ( $fields, $refers );

  }

  function padSelectWhereKeys ( &$where, $keys, $data, $type, $outer ) {

    if ( is_array ($keys) )
      foreach ( $keys as $key => $value )
        if ( $type )
          padSelectWhereAdd ( $where, $key, padSelectOuterValue ( $data, $value, $outer ) );
        else
          padSelectWhereAdd ( $where, $value, padSelectOuterValue ( $data, $key, $outer ) );
    else
      foreach ( padExplode ( $keys, ',' ) as $field )
        padSelectWhereAdd ( $where, $field, padSelectOuterValue ( $data, $field, $outer ) );

  }

  // The field of the enclosing select's current row that the relation is followed by. A
  // row without it - the outer tag's fields= left it out - ended the request on a PHP
  // undefined array key from inside the engine; it is named for what it is now, and under
  // the continuing error actions the condition compares with NULL and relates to no row.

  function padSelectOuterValue ( $data, $field, $outer ) {

    if ( is_array ( $data ) and array_key_exists ( $field, $data ) )
      return $data [$field];

    padError ( "the relation to '$outer' is followed by its field '$field', which the row of '$outer' does not hold - select it in fields=" );

    return NULL;

  }

  // The value - a key bound on the tag, often straight from the request, or a field of the
  // outer row - goes into the SQL string literal escaped for SQL, by the application's own
  // connection. It went through padEscape, which turns PAD's delimiters into entities and
  // leaves quotes alone: an apostrophe ended the literal and the rest of the value became
  // part of the condition, while an honest value holding a comma, = or @ was rewritten and
  // never matched.

  //
  // On SQLite a number, and a numeral, goes in as the number, as db()'s bare placeholders
  // have it (padDbLiteral): a column declared without a type has no affinity, so the text
  // '2' never equals the integer 2 there, and {boards $id=2} found no board and every
  // board's nested topics came out empty.

  function padSelectWhereAdd  (&$where, $field, $value) {

    $add = padSelectField ($field) . ' = ';

    if ( strpos ( $where, $add ) !== FALSE )
      return;

    if ($where) $where .= ' and ';
    else        $where  = 'where ';

    $connect = padDbApp ();

    // A NULL of the enclosing row relates to no row, as a join on it does: written as the
    // empty text, it found the row whose key is '' - on MySQL an integer key 0.

    if ( $value === NULL )
      $where .= $add . 'NULL';
    elseif ( $connect instanceof PDO and is_scalar ( $value ) )
      $where .= $add . padDbLiteral ( $connect, $value );
    else
      $where .= $add . "'" . padSelectEscape ($value) . "'";

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

      // One quoted literal and nothing else: the test was that the value starts with a quote,
      // so where='name = 1 or ' . $cond joined a condition from a variable into the SQL and
      // answered every row (padMetaLiteral gives a string for a single literal only).

      if ( $org !== '' and ! is_numeric ( $org ) and ! is_string ( padMetaLiteral ( $org ) ) )
        return padError ( "$part= is SQL the template writes: give it as a quoted string, and put a value in it as \$name" );

      $prm [$part] = padSelectBind ( $prm [$part], in_array ( $part, [ 'order', 'group' ] ) );

      if ( $prm [$part] === FALSE )
        return FALSE;

    }

    // fields=, db=, join= and union= go into the statement as SQL text too - a column list,
    // a table, a join, a table to add - and nothing is bound in them, so they are the
    // template's own text the same way: {t fields=$cols} took the column list from a
    // variable, and with $cols = "phone as name" from the request the select answered every
    // phone number under the name of a name; an array there went in key by key.

    foreach ( [ 'fields', 'db', 'join', 'union' ] as $part ) {

      if ( ! isset ( $prm [$part] ) or $prm [$part] === TRUE )
        continue;

      $org = '';

      foreach ( $padParms [$pad] ?? [] as $one )
        if ( ( $one ['padPrmName'] ?? '' ) === $part ) {
          $org = (string) ( $one ['padPrmOrg'] ?? '' );
          $org = trim ( str_contains ( $org, '=' ) ? substr ( $org, strpos ( $org, '=' ) + 1 ) : $org );
        }

      if ( $org !== '' and ! is_numeric ( $org ) and ! is_string ( padMetaLiteral ( $org ) ) )
        return padError ( "$part= is SQL the template writes: give it as a quoted string" );

    }

    return TRUE;

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
          $joinSQL .= padSelectJoinAdd ($value ['key'], $joinTable ['db'], $joinTable ['key'] ?? '') . ' ';
        }

      }

    }

    return $joinSQL;

  }

  // The join meets the fields of key= with the declared key of the joined table, one by
  // one. A joined table that declares no key - or a key of other length - left a field
  // without its partner, and the request ended on a PHP undefined array key from inside
  // the engine; it is named now, and the field is met by the column of its own name.

  function padSelectJoinAdd ($keys1, $db, $keys2) {

    $where = '';

    $values1 = padExplode ($keys1, ',');
    $values2 = padExplode ($keys2, ',');

    if ( count ( $values1 ) != count ( $values2 ) )
      padError ( "the join to '$db' on '$keys1' needs the key of '$db' declared with as many fields" );

    foreach ($values1 as $k => $v) {

      if ($where)
        $where .= ' and ';

      $where .= padSelectField($v) . ' = ' . padSelectField($db) . '.' . padSelectField( $values2 [$k] ?? substr ( strrchr ( ".$v", '.' ), 1 ) );

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

  // A column name, or table.column, quoted as an identifier: a backtick in the name is
  // doubled, as MySQL and SQLite read it. It was wrapped in backticks as it stood, so a
  // key= value holding one - "salary` desc #" - ended the identifier and wrote SQL of its
  // own into the statement. padSelectKeys quotes each key the same way now; it wrapped a
  // table.column as one name.

  function padSelectField ($field) {

    $quote = fn ( $name ) => '`' . str_replace ( '`', '``', $name ) . '`';

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  $quote ( $parts[0] ) . '.' . $quote ( $parts[1] );
    else
      return  $quote ( $parts[0] ?? '' );

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

  // A virtual table takes what it does not declare from its base, which is resolved the
  // same way, so a view of a view reaches the table at the end of the chain, and their
  // wheres both hold. Only the direct base was read: with no db of its own there, the view
  // selected from a table named after the middle view - "Table 'demo.sales' doesn't exist".
  // $seen ends a chain that comes back to a table already in it.

  function padSelectGetDB ( $table, $seen = [] ) {

    global $padSelect;

    if ( ! isset ( $padSelect [$table] ) )
      return [ 'db' => $table ];

    $parms = $padSelect [$table];
    $base  = $parms ['base'] ?? NULL;

    if ( is_string ( $base ) and isset ( $padSelect [$base] ) and $base !== $table and ! in_array ( $base, $seen, TRUE ) ) {

      $inherit = padSelectGetDB ( $base, array_merge ( $seen, [ $table ] ) );

      if ( isset ( $parms ['where'] ) and ( $inherit ['where'] ?? '' ) !== '' and $parms ['where'] !== '' )
        $parms ['where'] = '(' . $inherit ['where'] . ') and (' . $parms ['where'] . ')';

      unset ( $inherit ['base'] );

      foreach ( $inherit as $key => $value )
        if ( ! isset ( $parms [$key] ) )
          $parms [$key] = $value;

    }

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
