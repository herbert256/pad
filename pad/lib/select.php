<?php

  // The binding of the template's SQL: a $name outside a quoted literal becomes an escaped
  // literal of that application variable. The select subsystem binds its where=, having=,
  // order= and group= here (select/_lib/select.php, read when a page walks a select
  // table), and the database tags - {field}, {record}, {array}, {check} - bind their SQL
  // (padDbTagSql, lib/db.php).

  // Replaces each $name outside a quoted literal with the value of that application
  // variable: a quoted, escaped literal (a number as a number, NULL as NULL), or for a
  // column list the value as it is, which padSelectColumns then has to accept. The
  // database tags bind their SQL here too (padDbTagSql); $what names the asker.

  function padSelectBind ( $sql, $columns = FALSE, $what = 'the select' ) {

    $out   = '';
    $quote = '';
    $len   = strlen ( $sql );
    $mysql = ! ( padDbApp () instanceof PDO );

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      // A comment the author wrote in where= or having= is copied as it stands, its $name
      // left unbound: an apostrophe in it - where="/* the staff's pick */ name = $who" -
      // opened a quoted literal, so every $name after it was taken for quoted text and not
      // bound, and the select failed on "Unknown column '$who'". db()'s placeholder pass
      // skips comments the same way (padDbComment).

      if ( ! $quote and ( $end = padDbComment ( $sql, $i, $mysql ) ) ) {
        $out .= substr ( $sql, $i, $end - $i );
        $i    = $end - 1;
        continue;
      }

      // A backslash escapes the next character on MySQL only: SQLite has no backslash escape,
      // so 'C:\' is a whole literal there, and the quote that ends it was taken for an
      // escaped one - the rest of the statement counted as quoted, no $name in it was bound,
      // and SQLite read $n as a parameter of its own, NULL. padDbPlaceholders does the same.

      if ( $quote ) {
        if ( $mysql and $char == '\\' and $i + 1 < $len ) { $out .= $char . $sql [++$i]; continue; }
        if ( $char == $quote ) $quote = '';
      } elseif ( $char == "'" or $char == '"' or $char == '`' )
        $quote = $char;
      elseif ( $char == '$' and preg_match ( '/\G\$([A-Za-z][A-Za-z0-9_]*)/', $sql, $match, 0, $i ) ) {

        $name = $match [1];

        if ( ! padValidVar ( $name ) or ! array_key_exists ( $name, $GLOBALS ) )
          return padError ( "there is no application variable named \$$name for $what" );

        $value = $GLOBALS [$name];

        // An array is a list of literals, as in a bare db() placeholder - name in ($names) -
        // where it was escaped as one literal, 'jim,bob', which matched nothing, and on SQLite
        // was cast to text with an "Array to string conversion"; in order= or group= it holds
        // no column names.

        if ( $columns and ( ! is_scalar ( $value ) or ! padSelectColumns ( (string) $value ) ) )
          return padError ( "\$$name in order= or group= must hold column names, each with an optional asc or desc" );

        if     ( $columns                ) $add = (string) $value;
        elseif ( is_array ( $value )     ) $add = ( $connect = padDbApp () ) ? padDbLiteral ( $connect, $value ) : 'NULL';
        elseif ( $value === NULL         ) $add = 'NULL';
        elseif ( is_bool ( $value )      ) $add = $value ? '1' : '0';
        elseif ( is_int ( $value ) or is_float ( $value ) or ( ! $mysql and padDbNumeral ( (string) $value ) ) )
                                           $add = (string) $value;
        else                               $add = "'" . padSelectEscape ( $value ) . "'";

        // A bare negative number right after a minus - where="salary > 1000-$n", $n = -1 -
        // would join into "1000--1", a -- line comment on SQLite that swallows the rest of
        // the clause (MySQL wants white space after --). A space keeps the two minuses apart.

        if ( $add !== '' and $add [0] === '-' and $out !== '' and $out [ strlen ( $out ) - 1 ] === '-' )
          $add = " $add";

        $out .= $add;

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

  // Escaped for a single-quoted literal, the way the application's database driver wants
  // it - backslashes for MySQL, a doubled quote for SQLite (lib/db.php).

  function padSelectEscape ( $value ) {

    $connect = padDbApp ();

    if ( ! $connect )
      return str_replace ( "'", "''", (string) ( $value ?? '' ) );

    return padDbEscape ( $connect, $value, "'" );

  }

?>
