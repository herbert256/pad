<?php

  // How a .sql migration is split into statements: on ; outside quotes and comments, a
  // doubled quote inside a literal, the body of a trigger kept whole - its IF and CASE
  // statements too -, a DELIMITER line changing the separator, and on MySQL a backslash
  // escape and a # comment.

  $sql = "create table a (x varchar(9) default 'it''s; ok'); -- a comment; with a semicolon\n"
       . "/* and ; another */ insert into a values (\"q;q\");\n"
       . "create trigger t after insert on a begin update a set x = case when 1 then 'y' end; delete from a; end;\n"
       . "DELIMITER $$\n"
       . "create procedure p() begin select 1; select 2; end$$\n"
       . "DELIMITER ;\n"
       . "select 3";

  $sqlite = padMigrateSplit ( $sql, FALSE );
  $mysql  = padMigrateSplit ( "insert into a values ('a\\';b'); # note; here\nselect 4;", TRUE );

  // A MySQL trigger whose body has IF ... END IF and CASE ... END CASE statements: END IF
  // closes a block whose IF is not counted, END CASE the CASE that is - one statement.

  $blocks = padMigrateSplit ( "create trigger u before insert on a for each row begin if new.x < 0 then set new.x = 0; end if; "
                            . "case when new.x > 9 then set new.x = 9; else set new.x = 1; end case; end; select 5", TRUE );

?>
