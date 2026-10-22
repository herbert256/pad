<?php

  // BEGIN, END and CASE are no reserved words in SQLite or MySQL, and begin/end is a common
  // pair of date columns: written through its row - new.end, old.begin - in a list of names,
  // or set with =, the word is a name, and the trigger stays one statement with what follows
  // it a statement of its own.

  $r = json_encode ( [
    'row'  => padMigrateSplit ( "create trigger a after insert on e begin insert into l (id, note) values (new.id, new.end); "
                              . "insert into l (id, note) values (old.begin, 'x'); end; select 1", FALSE ),
    'set'  => padMigrateSplit ( "create trigger b after insert on e begin update e set begin = 1, end = 2, case = 3 where id = new.id; end; select 2", FALSE ),
    'list' => padMigrateSplit ( "create trigger c after insert on e for each row begin insert into f (title, begin, end) values (new.title, new.begin, new.end); end; select 3", TRUE ),
    'case' => padMigrateSplit ( "create trigger d after insert on e begin update e set x = (case when new.end > 1 then 1 else 2 end), y = case new.begin when 1 then 2 end; end; select 4", FALSE ),
  ] );

?>
