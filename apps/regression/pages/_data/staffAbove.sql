-- The staff earning more than a minimum, through a common table expression
with paid as (select name, salary from staff where salary > {$minimum})
select name from paid order by name
