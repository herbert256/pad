-- A WITH that reads: replace() in its select list, a write word in a literal.
with t as ( select replace ( name, 'o', '0' ) as n from staff where name <> 'update' )
select n from t order by n
