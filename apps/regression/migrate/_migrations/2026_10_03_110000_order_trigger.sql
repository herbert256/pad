-- A trigger: the semicolons between BEGIN and END are its body, not the end of the statement.

create trigger orders_logged after insert on orders
begin
  insert into order_log (order_id, note) values (new.id, 'placed');
  insert into order_log (order_id, note) values (new.id, case when new.total > 100 then 'big' else 'small' end);
end;
