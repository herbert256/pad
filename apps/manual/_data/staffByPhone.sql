-- The staff whose phone number matches a pattern, at most so many of them.
select name, phone
  from staff
 where phone like {$pattern}
 order by name
 limit {$max}
