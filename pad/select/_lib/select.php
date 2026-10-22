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

    // A where= on the tag narrows the table the declaration makes: the declared where - a
    // view's, with those of its base chain - holds beside it. The tag's took its place, so
    // {jazzAlbums where="released > 2000"} answered the folk albums too, and a view of the
    // published posts handed out a draft by its slug. An empty where= narrows nothing: it
    // took the declared where's place, and the view answered every row of its table.

    $tagWhere = ( (string) ( $prm ['where'] ?? '' ) !== '' );

    if ( ! $tagWhere )
      $padSelWhere = $parms ['where'] ?? '';
    elseif ( (string) ( $parms ['where'] ?? '' ) !== '' )
      $padSelWhere = padSelectWherePart ( $parms ['where'] ) . ' and ' . padSelectWherePart ( $prm ['where'] );

    // The mode becomes the statement's command word, so it is one of the shapes db() knows
    // for rows - array, record or field - and a bare htmlAttrJson means array. Any other
    // value went into the SQL as written.

    if ( $htmlAttrJson === TRUE or $htmlAttrJson === 1 or $htmlAttrJson === '1' )
      $htmlAttrJson = 'array';

    if ( $htmlAttrJson and ! in_array ( strtolower ( (string) $htmlAttrJson ), [ 'array', 'record', 'field' ], TRUE ) )
      return padError ( "htmlAttrJson= takes array, record or field, not '" . padMakeSafe ( (string) $htmlAttrJson, 40 ) . "'" );

    if ( ! $padHtmlAttrJson and $htmlAttrJson and ! $unionBuild ) {
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

    // The order a table declares - or its key, when it declares none - is the order of its
    // rows. A select that makes rows of its own - groups, a rollup, distinct rows, totals in
    // its fields, a union - is sorted by an order given with those alone: the tag's order=,
    // or the order of the declaration that makes them. The declared one was added to it, so
    // a rollup over a declared table with an order or a key ended on "Incorrect usage of
    // CUBE/ROLLUP and ORDER BY", under ONLY_FULL_GROUP_BY, MySQL's default, so did
    // {albums fields='count(*) as n'} - "Mixing of GROUP columns ... with no GROUP BY" - and
    // a union whose columns leave the order out on "Unknown column ... in 'ORDER BY'": after
    // a union only its own columns can be named.

    $grouped = padSelectMakesRows ( $parms );
    $ordered = ( $grouped or ! padSelectMakesRows ( $prm ) );

    // MariaDB, and MySQL before 8.0.12, take no ORDER BY beside WITH ROLLUP, whose rows come
    // in the order of the group: with a rollup only an order= on the tag is written, not
    // the order of the declaration that groups.

    if ( ! isset ( $prm ['order'] ) and ( ! $ordered or $rollup ) )
      $padSelOrder = '';

    $padSelStart = padSelectStart  ( $all, $distinct, $distinctrow);
    $padSelGroup = padSelectGroup  ( $padSelGroup, $rollup );
    $having      = padSelectHaving ( $having );
    $padSelLimit = padSelectLimit  ( $rows, $page, array_key_exists ( 'page', $prm ) or array_key_exists ( 'rows', $prm )
                                                  or array_key_exists ( 'page', $parms ), $unionBuild, array_key_exists ( 'rows', $prm ) );
    $padSelWhere = padSelectWhere  ( $padSelWhere, $table, $keys, $tagWhere, padSelectOwn ( $db, $padSelJoin ) );
    $fields      = padSelectFields ( $fields, $db );
    $padSelJoin  = padSelectJoin   ( $padSelJoin, $fields );
    $keys        = padSelectKeys   ( $keys );
    $padSelOrder = padSelectOrder  ( $padSelOrder, $padSelJoin, ( $ordered and ! $grouped ) ? $keys : '' );

    // The outer query's parts are folded into text before the union members are built:
    // the composition variables are globals - the dump reads them, under pad-prefixed names
    // so a $where or $order of the application is not overwritten - and a member's
    // build writes its own parts into the same names, so a union query composed after it
    // carried the member's where instead of its own.
    //
    // Each clause stands on a line of its own (padSelectLines): a -- or # comment written in
    // one ran to the end of the statement on one line, so a comment in group=, having= or
    // order= took the clauses after it - the having, the order, the limit - without a word,
    // and one in where= the closing parenthesis of the where.

    $head  = padSelectLines ( [ "$padSelStart $fields", "from $db", $padSelJoin, $padSelWhere, $padSelGroup, $having ] );
    $tail  = padSelectLines ( [ $padSelOrder, $padSelLimit ] );
    $limit = $padSelLimit;
    $join  = $padSelJoin;

    $padSelUnion = padSelectUnion  ( $padSelUnion );
    $members     = $padSelUnion;

    $_UNION [] = $padSelUnion;

    $base  = padSelectLines ( [ $head, $padSelUnion ] );
    $sql   = "$type " . padSelectLines ( [ $base, $tail ] );
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
    //
    // The limit is the one folded into $tail: building the union members sets the global
    // again, to theirs, so a paged select with union= booked no count and its {pager} showed
    // no links - or, read from a select before it on the same level, that one's pages. A
    // select that books none takes the count of the one before away for the same reason.
    // The join is this select's own the same way: the last member's was read, and a joined
    // select with a union that had none ended its pager on "Duplicate column name".

    if ( ! $unionBuild and ! $limit )
      unset ( $GLOBALS ['padPagerCount'] [$pad], $GLOBALS ['padPagerRows'] [$pad] );

    if ( $limit and ! $unionBuild ) {
      $short = ( in_array ( $padSelStart, [ '', 'ALL' ] ) and ! $padSelGroup and ! $having and trim ( $members ) === ''
                 and ! padSelectTotals ( (string) $fields ) );
      $count = $short
             ? 'select 1 ' . padSelectLines ( [ "from $db", $padSelJoin, $padSelWhere ] )
             : "select $base";
      // A select list may hold a column name twice - select distinct * from orders join
      // customers, both with customerNumber - which no derived table can, and the count
      // ended on "Duplicate column name": it is the number of rows the select gives without
      // its limit then, read when the pager asks (padPagerItems). Only then: every joined
      // select was read that way, and the pager of 350,000 distinct rows took 170 MB.

      $GLOBALS ['padPagerCount'] [$pad] = ( ! $short and padSelectRepeats ( (string) $fields, $join !== '' ) )
                                        ? "array $base"
                                        : "field count(*) from ( $count\n) as padPagerCount";
      $GLOBALS ['padPagerRows']  [$pad] = $rows;
    }

    // A member with rows= of its own - and the order that picks them - keeps them: its order
    // and limit were left out, and every row of it was added without a word. MySQL takes the
    // member in parentheses, as it stands; SQLite has no parentheses there and reads it as a
    // derived table. A derived table on MySQL may not hold a column name twice, and a member
    // of orders joined to customers, both with customerNumber, ended on "Duplicate column
    // name".

    if ( $unionBuild and $limit )
      return ( padDbApp () instanceof PDO )
           ? "union select * from (\nselect " . padSelectLines ( [ $base, $tail ] ) . "\n) as padUnion" . ( count ( $_UNION ) )
           : "union (\nselect " . padSelectLines ( [ $base, $tail ] ) . "\n)";

    if ($unionBuild)
      return $padSelUnion;
    else
      return db ( $sql );

  }

  // Whether options - the tag's, or a table's declaration - make rows of their own: groups,
  // a rollup, distinct rows, totals in the fields, or a union.

  function padSelectMakesRows ( $options ) {

    foreach ( [ 'group', 'rollup', 'distinct', 'distinctrow', 'union' ] as $one )
      if ( ! empty ( $options [$one] ) )
        return TRUE;

    return is_string ( $options ['fields'] ?? NULL ) and padSelectTotals ( $options ['fields'] );

  }

  // A column list that starts with distinct, or holds an aggregate: one row of totals, or a
  // row per distinct value. An aggregate in a subquery - (select max(released) from albums
  // where ...) - or over a window - sum(price) over (order by released) - is a value of each
  // row, so those are taken out first: they made the select one of totals, and it lost the
  // declared order, with rows= another first page. over is a word of its own there: the
  // alias of sum(salary) overall is no window.

  function padSelectTotals ( $fields ) {

    if ( preg_match ( '/^\s*(all\s+)?distinct(row)?\b/i', $fields ) )
      return TRUE;

    $group  = '(?(DEFINE)(?<group>\((?:[^()\'"]++|\'[^\']*\'|"[^"]*"|(?&group))*\)))';
    $fields = preg_replace ( "/\\(\\s*select\\b(?:[^()'\"]++|'[^']*'|\"[^\"]*\"|(?&group))*\\)$group/i", '()', $fields ) ?? $fields;
    $fields = preg_replace ( "/\\b\\w+\\s*(?&group)\\s*over\\b\\s*(?:(?&group)|\\w+)$group/i", '0', $fields ) ?? $fields;

    return (bool) preg_match ( '/\b(count|sum|avg|min|max|total|group_concat|json_arrayagg|json_objectagg|string_agg|bit_and|bit_or|bit_xor|std|stddev|stddev_pop|stddev_samp|variance|var_pop|var_samp)\s*\(/i', $fields );

  }

  // Whether the columns of a select list can repeat a name: a * beside a join or beside
  // another column, two columns of one name or alias, or a list with a comment, which is
  // not read further.

  function padSelectRepeats ( $fields, $joined ) {

    if ( preg_match ( '/--|#|\/\*/', $fields ) )
      return TRUE;

    $items = padSelectItems ( preg_replace ( '/^\s*(all|distinctrow|distinct)\b\s*/i', '', $fields ) );
    $names = [];

    foreach ( $items as $item ) {

      // A column in parentheses is named by what they hold: distinct(albums.artist_id) is
      // artist_id, as a column beside it of that name.

      while ( preg_match ( '/^(\((?:[^()\'"]++|\'[^\']*\'|"[^"]*"|(?1))*\))$/s', $item ) )
        $item = trim ( substr ( $item, 1, -1 ) );

      if ( $item === '*' or str_ends_with ( $item, '.*' ) ) {
        if ( $joined or count ( $items ) > 1 )
          return TRUE;
        continue;
      }

      $name = '(`[^`]+`|"[^"]+"|\'[^\']+\'|\w+)';

      if (     preg_match ( "/\\bas\\s+$name$/i",                         $item, $match ) ) $item = $match [1];
      elseif ( preg_match ( "/^(?:(?:`[^`]+`|\\w+)\\s*\\.\\s*)*$name$/", $item, $match ) ) $item = $match [1];
      elseif ( preg_match ( "/[\\w`)]\\s+$name$/",                       $item, $match ) ) $item = $match [1];

      $item = strtolower ( preg_replace ( '/\s+/', ' ', trim ( $item, '`"\'' ) ) );

      if ( isset ( $names [$item] ) )
        return TRUE;

      $names [$item] = TRUE;

    }

    return FALSE;

  }

  // The items of a select list, split at the commas outside parentheses and quotes.

  function padSelectItems ( $fields ) {

    $items = [];
    $item  = '';
    $depth = 0;
    $in    = '';

    for ( $i = 0, $len = strlen ( $fields ); $i < $len; $i++ ) {

      $char = $fields [$i];

      if     ( $in )                          { if ( $char == $in ) $in = ''; }
      elseif ( strpos ( '\'"`', $char ) !== FALSE ) $in = $char;
      elseif ( $char == '(' )                 $depth++;
      elseif ( $char == ')' )                 $depth--;
      elseif ( $char == ',' and ! $depth )    { $items [] = trim ( $item ); $item = ''; continue; }

      $item .= $char;

    }

    $items [] = trim ( $item );

    return $items;

  }

  // The clauses of a statement, each on a line of its own and the empty ones left out, so a
  // line comment written in one ends with it.

  function padSelectLines ( $parts ) {

    return implode ( "\n", array_filter ( array_map ( 'trim', $parts ), fn ( $one ) => $one !== '' ) );

  }

  function padSelectKeys ( $keys ) {

    if ( ! $keys )
      return '';

    return implode ( ',', array_map ( 'padSelectField', padExplode ( $keys, ',' ) ) );

  }

  function padSelectStart ( $all,  $distinct, $distinctrow ) {

    // distinctrow is MySQL's word for distinct: SQLite read it as a column name, so there
    // the select writes distinct.

    if     ($all)         return 'ALL';
    elseif ($distinct)    return 'distinct';
    elseif ($distinctrow) return ( padDbApp () instanceof PDO ) ? 'distinct' : 'distinctrow';
    else                  return '';

  }

  function padSelectGroup ( $group, $rollup ) {

    if ($group)
      $group = "group by $group";

    // WITH ROLLUP is MySQL's, and SQLite ended the statement on 'near "with": syntax
    // error'; it is refused with what it is, and under a continuing error action the
    // select runs without the total row.

    if ( $rollup and padDbApp () instanceof PDO )
      padError ( "rollup is MySQL's WITH ROLLUP, which SQLite has not got - total the rows in a select of their own" );
    elseif ($rollup)
      $group .= "\nwith rollup";

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
  //
  // $given: page= or rows= is written on the tag, or page in the declaration. The limit was
  // made when their values were, so page=$pg ?? 1 with ?pg= or ?pg=0 read the whole table,
  // the handling paged it in PHP, and a sort= before the page made its first page another
  // one than ?pg=1's. A declaration's 'rows' => 0 alone is no limit, as it always was.

  //
  // $member: the limit of a union member, which is its own and none of the level's - the
  // book is neither read nor marked for it.
  //
  // $rowsGiven: rows= is written on the tag. Below one - 0, a request value that is no
  // number - it keeps no row, as it does on every other data; it was read as the 10 of a
  // rows= not written.

  function padSelectLimit ( &$rows, &$page, $given = FALSE, $member = FALSE, $rowsGiven = FALSE ) {

    global $pad, $padDone;

    $limit = '';

    // Numbers, whatever the options held: they are spliced into the statement as written.

    $rows = (int) $rows;
    $page = (int) $page;

    if ( $member or ( $padDone [$pad] ['page'] ?? '' ) !== 'limit' )

      if ( $given or $page or $rows ) {

        if ($rows < 1) $rows = $rowsGiven ? 0 : 10;
        if ($page < 1) $page = 1;

        // A page from the request past PHP_INT_MAX / rows made the offset a float, written
        // 1.844674407371E+19 - an SQL syntax error a visitor could raise at will. Held there,
        // it is a page past the last, as any page too high is.

        $page   = $rows ? min ( $page, intdiv ( PHP_INT_MAX, $rows ) ) : 1;
        $offset = ($page-1) * $rows;
        $limit = "limit $offset, $rows";

        if ( ! $member ) {
          padDone ('page', 'limit');
          padDone ('rows', 'limit');
        }

      }

    return $limit;

  }

  // The table a select with a join names its key and relation fields with - those it holds
  // (padSelectHolds): a field both tables hold - orderNumber in orders and orderdetails - was
  // written bare, and the select ended on "Column 'orderNumber' in WHERE is ambiguous" as
  // soon as a key was bound on its tag or it stood in a related select. A db that is no
  // plain name - a schema, an alias - leaves the fields bare, as before.

  function padSelectOwn ( $db, $join ) {

    if ( is_array ( $join ) ? ! $join : trim ( (string) $join ) === '' )
      return '';

    return preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', (string) $db ) ? $db : '';

  }

  // Whether the table $db holds the column $field: its columns are read once a request -
  // SHOW COLUMNS on MySQL, PRAGMA table_info on SQLite - on the connection itself, so the
  // application's _events/sql.php does not hear them as a statement of the page. TRUE when
  // they cannot be read.

  function padSelectHolds ( $db, $field ) {

    static $columns = [];

    if ( ! array_key_exists ( $db, $columns ) ) {

      $connect = padDbApp ();
      $names   = [];

      if ( $connect instanceof PDO and $result = $connect->query ( 'pragma table_info(' . padSelectField ( $db ) . ')' ) )
        $names = array_column ( $result->fetchAll ( PDO::FETCH_ASSOC ), 'name' );
      elseif ( $connect instanceof mysqli and $result = mysqli_query ( $connect, 'show columns from ' . padSelectField ( $db ) ) )
        $names = array_column ( mysqli_fetch_all ( $result, MYSQLI_ASSOC ), 'Field' );

      $columns [$db] = $names ? array_flip ( array_map ( 'strtolower', $names ) ) : NULL;

    }

    return $columns [$db] === NULL or isset ( $columns [$db] [ strtolower ( trim ( $field, '` ' ) ) ] );

  }

  // $tagWhere: the where= was written on the tag, not taken from the declaration. $own: the
  // table the fields are named with (padSelectOwn), or ''.

  function padSelectWhere ( $where, $table, $keys, $tagWhere = FALSE, $own = '' ) {

    global $pad, $padRelations, $padCurrent, $padTag, $padType, $padSetLvl, $padElse;

    if ($where)
      $where = 'where ' . padSelectWherePart ( $where );

    $bound = FALSE;

    foreach ( padExplode ( $keys, ',' ) as $field )
      if ( isset ( $padSetLvl [$pad] [$field] ) ) {
        padSelectWhereAdd ( $where, "$field", $GLOBALS [$field], $own );
        $bound = TRUE;
      }

    // Each enclosing select is related to this one by the first of this table's names - its
    // own, then each base= it stands on - that has a relation to the outer table or to a
    // table of the outer's base= chain. Only the outer's chain was walked: a view of
    // employees nested in {offices} was related by nothing, and every office listed every
    // row of the view.

    // A select rendering its @else@ - it found no row - has no row to relate to, so it is
    // passed over: the select nested there stands alone. Its empty row was followed, and
    // the page ended on "... which the row of 'customers' does not hold - select it in
    // fields=". Only that one: a select of one value - type='field' - was passed over as
    // well, and the select in it listed every row of its table without a word.

    $padSelOuter = '';

    for ( $i=$pad-1; $i; $i-- )
      if ( $padType [$i] == 'select' and ! ( $padElse [$i] ?? FALSE ) ) {

        // The strict check names the select this one stands in, the nearest: the last one
        // walked was named, the outermost, which the select was never written in.

        if ( $padSelOuter === '' )
          $padSelOuter = $padTag [$i];

        foreach ( padSelectChain ( $table ) as $inner ) {

          $found = FALSE;

          foreach ( padSelectChain ( $padTag [$i] ) as $relation )
            if ( padSelectWhereRelation ( $where, $inner, $relation, $padCurrent [$i], $own ) )
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

  // A condition in parentheses, the closing one on a line of its own when a -- or # comment
  // in it would take it: the where of a view composed with its base's, or with the tag's,
  // ended on an SQL syntax error when one of them held a comment.

  function padSelectWherePart ( $where ) {

    return '(' . $where . ( preg_match ( '/--|#/', (string) $where ) ? "\n)" : ')' );

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

  function padSelectWhereRelation ( &$where, $table, $relation, $data, $own = '' ) {

    global $padRelations;

    // A relation declared empty - [] or '', the form classicModels once wrote them all in -
    // names no field to follow: it relates nothing, as one never declared, and the strict
    // check says so. It counted as a relation, and every row met every row without a word.

    $empty = fn ( $keys ) => $keys === NULL or $keys === [] or $keys === '';

    if  ( ! $empty ( $padRelations [$relation] [$table] ?? NULL ) )
      padSelectWhereKeys ( $where, padSelectRelationKeys ( $padRelations [$relation] [$table], $relation, $table ), $data, 0, $relation, $own );
    elseif ( ! $empty ( $padRelations [$table] [$relation] ?? NULL ) )
      padSelectWhereKeys ( $where, padSelectRelationKeys ( $padRelations [$table] [$relation], $table, $relation ), $data, 1, $relation, $own );
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

  function padSelectWhereKeys ( &$where, $keys, $data, $type, $outer, $own = '' ) {

    if ( is_array ($keys) )
      foreach ( $keys as $key => $value )
        if ( $type )
          padSelectWhereAdd ( $where, $key, padSelectOuterValue ( $data, $value, $outer ), $own );
        else
          padSelectWhereAdd ( $where, $value, padSelectOuterValue ( $data, $key, $outer ), $own );
    else
      foreach ( padExplode ( $keys, ',' ) as $field )
        padSelectWhereAdd ( $where, $field, padSelectOuterValue ( $data, $field, $outer ), $own );

  }

  // The field of the enclosing select's current row that the relation is followed by. A
  // row without it - the outer tag's fields= left it out - ended the request on a PHP
  // undefined array key from inside the engine; it is named for what it is now, and under
  // the continuing error actions the condition compares with NULL and relates to no row.

  //
  // A select that answers one value - type='field', htmlAttrJson - has no row at all, and is
  // named for that: fields= was not what to change.

  function padSelectOuterValue ( $data, $field, $outer ) {

    if ( is_array ( $data ) and array_key_exists ( $field, $data ) )
      return $data [$field];

    if ( ! is_array ( $data ) or ! $data )
      padError ( "the relation to '$outer' is followed by its field '$field', but '$outer' answers one value here, no row - "
               . "a select of type='field' or with htmlAttrJson has none to follow" );
    else
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

  //
  // $own names the field only when its table holds it (padSelectHolds): a field of the
  // joined table - the label an album's artist is signed to - was named with the select's
  // own table, and the select ended on "Unknown column 'albums.signed_to'".

  function padSelectWhereAdd  (&$where, $field, $value, $own = '') {

    $add = padSelectField ( ( $own !== '' and ! str_contains ( $field, '.' ) and padSelectHolds ( $own, $field ) ) ? "$own.$field" : $field ) . ' = ';

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

    // A value spliced into an option more than 4 KB past the tag's { is judged here, as one
    // nearer is where it is spliced (padDbSpliceFar, lib/db.php).

    if ( $prm and ! padDbSpliceFar () )
      return FALSE;

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

 // $seen: the views whose own joins are being written - a view joined inside itself is
 // joined without them.

 function padSelectJoin ( $join, &$fields, $seen = [] ) {

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

        // A joined table with a where - a view - is joined as the rows of its where, a
        // derived table under the table's own name, so the key and the fields still name
        // it, for every kind of join; its where was left out and every row joined. A name in
        // backticks - a table that is a reserved word - is one name as well, and joined
        // every row. The view's own joins go inside with it - its where may name their
        // tables, and ended on "Unknown column 'offices.city'" - and only its own columns
        // come out, under their own names.

        $joinFrom = $joinTable ['db'];

        if ( (string) ( $joinTable ['where'] ?? '' ) !== '' and preg_match ( '/^([A-Za-z_][A-Za-z0-9_]*|`[^`]+`)$/', (string) $joinFrom ) ) {

          $none  = '';
          $joins = ( ! empty ( $joinTable ['join'] ) and ! in_array ( $table, $seen, TRUE ) )
                 ? trim ( padSelectJoin ( $joinTable ['join'], $none, array_merge ( $seen, [ $table ] ) ) ) : '';

          $joinFrom = "(\nselect " . ( $joins === '' ? '*' : "$joinFrom.*" ) . " from $joinFrom"
                    . ( $joins === '' ? '' : "\n$joins" ) . "\nwhere " . $joinTable ['where'] . "\n) as $joinFrom";

        }

        $joinSQL .= ' ' . $xtype .  ' join ' . $joinFrom . ' ';

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

  //
  // A name already in backticks, with none inside - a table declared `order`, the way to name
  // one that is a reserved word - is quoted as it stands: quoted again it was ```order```,
  // and a join to it ended on "Unknown column".

  function padSelectField ($field) {

    $quote = fn ( $name ) => preg_match ( '/^`[^`]+`$/', $name ) ? $name : '`' . str_replace ( '`', '``', $name ) . '`';

    $parts = padExplode($field, '.');

    if ( count($parts) == 2 )
      return  $quote ( $parts[0] ) . '.' . $quote ( $parts[1] );
    else
      return  $quote ( $parts[0] ?? '' );

  }

  // The columns of a joined table go after the select's own on a line of their own when a
  // -- or # comment ends that list: written after it, they were part of the comment, and the
  // joined columns were missing from every row.

  function padSelectAddFields (&$result, $table, $fields) {

    $comma = fn () => preg_match ( '/--|#/', $result ) ? "\n," : ',';

    if ( is_array($fields) ) {
      foreach ($fields as $key => $value) {
        if ($result)
          $result .= $comma ();
        $result .= ' ' . $table . '.' . $key . ' as ' . $value;
      }
    } else {
      if ($result)
        $result .= $comma ();
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
        $parms ['where'] = padSelectWherePart ( $inherit ['where'] ) . ' and ' . padSelectWherePart ( $parms ['where'] );

      unset ( $inherit ['base'] );

      // A view that makes rows of its own is not sorted by the order of the table it stands
      // on, the order of that table's rows (padSelect).

      if ( padSelectMakesRows ( $parms ) )
        unset ( $inherit ['order'] );

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
