<?php

  // How a .sql migration is split into statements: on ; outside quotes and comments, a
  // doubled quote inside a literal, the body of a trigger kept whole, a DELIMITER line
  // changing the separator, and on MySQL a backslash escape and a # comment.

  $sql = "create table a (x varchar(9) default 'it''s; ok'); -- a comment; with a semicolon\n"
       . "/* and ; another */ insert into a values (\"q;q\");\n"
       . "create trigger t after insert on a begin update a set x = case when 1 then 'y' end; delete from a; end;\n"
       . "DELIMITER $$\n"
       . "create procedure p() begin select 1; select 2; end$$\n"
       . "DELIMITER ;\n"
       . "select 3";

  $sqlite = padMigrateSplit ( $sql, FALSE );
  $mysql  = padMigrateSplit ( "insert into a values ('a\\';b'); # note; here\nselect 4;", TRUE );

?>
