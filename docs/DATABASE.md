# DATABASE.md - PAD Database Reference

This file documents database operations in PAD, including the `db()` function, template database tags, and the PAD Select subsystem.

---

## PHP db() Function

The `db()` function is the primary way to interact with the database from PHP code.

```php
// RECORD - Single row
$user = db("RECORD * FROM users WHERE id={0}", [$id]);

// ARRAY - Multiple rows
$users = db("ARRAY * FROM users ORDER BY name");

// FIELD - Single value
$count = db("FIELD COUNT(*) FROM users");

// CHECK - Boolean (special syntax - NO "* FROM")
$exists = db("CHECK users WHERE email='{0}'", [$email]);

// INSERT - Returns ID
$id = db("INSERT INTO users (name) VALUES ('{0}')", [$name]);

// UPDATE - Updates rows
db("UPDATE users SET name='{0}' WHERE id={1}", [$name, $id]);
```

**Placeholders:** a placeholder written inside quotes gets the value escaped. One written
bare gets a number as a number and anything else as a quoted, escaped literal, so a bare
`id={0}` cannot take `5 or 1=1` as SQL. On MySQL text that looks like a number - every request
value - is text too, as MySQL would compare a text column with the number (`name={0}` with `'0'`
matched every name), except where MySQL wants a number - after `LIMIT`, `OFFSET` and
`FETCH FIRST`/`NEXT`, as a window frame's bound (`ROWS BETWEEN {0} PRECEDING`), and as an item
of an `ORDER BY` or `GROUP BY` list written in the statement (not in a raw `{x…}` key), where numeric text is
a column position, so a request value there must name one of the selected columns (`'9'` on a
three-column select is an SQL error); SQLite keeps it a number. An array written bare becomes a list. Placeholders are
filled in one pass, so a value holding `{1}` stays as written. Keys starting with `x` are
inserted raw - a deliberate escape hatch for SQL the code builds itself, never for input.
```php
db("SELECT * FROM users WHERE name='{0}'", [$name]);      // escaped inside the quotes
db("SELECT * FROM users WHERE name={0}", [$name]);        // quoted for you
db("SELECT * FROM users WHERE id IN ({0})", [[1, 2, 3]]);  // a list
```

**During a replay** of recorded traffic (`$padRecord`, `develop/?replay`) `db()` never
writes: a statement that would change the database - INSERT, UPDATE, DELETE, REPLACE,
TRUNCATE, LOAD, CREATE, DROP, ALTER, RENAME, GRANT, REVOKE, CALL, LOCK - is not sent and
answers 0, as if no row changed. Reads run as usual.

---

## Template Database Tags

The tag name becomes the `db()` command word - `{field "X"}` runs `db("field X")`, and so on. Do NOT write `SELECT` in the tag parameter; the command adds it.

### {field} - Query Single Value

```
{field "count(*) from users"}
{field "name from users where id=5"}
```

### {record} - Query Single Record

```
{record "* from users where id=5"}
  Name: {$name}, Email: {$email}
{/record}
```

### {array} - Query and Iterate Rows

```
{array "* from users order by name"}
  <tr><td>{$name}</td><td>{$email}</td></tr>
{/array}
```

### {check} - Boolean Existence Test

Uses the same special syntax as `db("CHECK ...")` - table name first, NO `* FROM`:

```
{check "users where email='x@y.z'"}
  Address is already registered.
{/check}
```

To iterate a whole table by name without writing SQL, use the PAD Select subsystem below.

### Named queries - `_data/*.sql`

A `.sql` file in `_data/` is a query with a name: the tag of that name runs it and iterates
the rows.

**_data/staffByPhone.sql:**
```sql
-- The staff whose phone number matches a pattern
select name, phone
  from staff
 where phone like {$pattern}
 order by name
 limit {$max}
```

```
{staffByPhone}{$name} {/staffByPhone}                    # $pattern and $max of the page
{staffByPhone pattern='%3%', max=2}{$name} {/staffByPhone}  # options of the tag win
```

- The file is never run as PAD, so no value is ever spliced into the SQL text: each
  `{$name}` is bound through the `db()` placeholders - an escaped literal, a number as a
  number, an array as a list for `IN ({$ids})`.
- `{$name}` is looked up as an option of the tag first, then as a field or a variable of
  the page; under the strict check a name that is neither is an error.
- The statement must read: `select`, or one of the `array`, `record`, `field` and `check`
  forms of `db()`. Lines starting with `--` are comments.
- `{local:staffByPhone.sql}` names the file explicitly.

---

## PAD Select Subsystem

PAD Select allows templates to access database tables directly without writing PHP queries. Define tables and relations in `_lib/select.php`, then use table names as tags.

### Configuration (_lib/select.php)

```php
// Define tables with primary key
$padSelect ['users']         = [ 'key' => 'id' ];
$padSelect ['forum_boards']  = [ 'key' => 'id' ];
$padSelect ['forum_topics']  = [ 'key' => 'id' ];
$padSelect ['forum_posts']   = [ 'key' => 'id', 'order' => 'created_at' ];
$padSelect ['news']          = [ 'key' => 'id' ];

// Define relations (foreign keys)
$padRelations ['forum_topics'] ['forum_boards'] = [ 'key' => 'board_id' ];
$padRelations ['forum_topics'] ['users']        = [ 'key' => 'user_id'  ];
$padRelations ['forum_posts']  ['forum_topics'] = [ 'key' => 'topic_id' ];
$padRelations ['forum_posts']  ['users']        = [ 'key' => 'user_id'  ];

// Virtual tables (filtered/sorted views)
$padSelect ['openBugs'] = [
    'base'  => "tickets",
    'where' => "`type`='bug' and `status`='open'",
    'order' => "updated_at desc"
];
```

### Using Table Tags in Templates

```html
<!-- List all users -->
{users}
  {$username} - {$email}
{/users}

<!-- Filter by ID -->
{users $id=5}
  {$username}
{/users}

<!-- Filter by another field - $field=value on the tag binds the declared key only -->
{forum_boards where="slug = $slug"}
  {$name}
{/forum_boards}

<!-- The ten newest: order= sorts in the SQL, before rows= - its LIMIT - takes the ten -->
{news order="created_at desc", rows=10}
  {$title}
{/news}

<!-- A condition of your own: write it as a quoted string, put values in as $name -->
{customers where="country = $country and creditLimit > $minimum", order="$sortColumn"}
  {$customerName}
{/customers}
```

On a select, `rows=` (and `page=`) is the SQL `LIMIT`, applied before any handling option:
`sort=` re-sorts the rows that came back, so `{news sort="created_at desc", rows=10}` is the
first ten rows of the table, sorted - `order=` is what sorts before the limit.

`where=`, `having=`, `order=` and `group=` written on a tag are SQL, so they must be quoted
strings in the template; `where=$cond` is refused, and so is a `fields=`, `db=`, `join=` or
`union=` taken from a variable. Inside the first four a `$name` is bound by the select: in
`where=`/`having=` as a quoted, escaped literal of that application variable (a number as a
number, an array as a list - numeric text is text on MySQL, as for `db()`), in
`order=`/`group=` as column names, each with an optional `asc`/`desc`, and nothing
else. `{$name}` there splices text into your SQL instead - use `$name`. Keys bound on the tag
(`{users $id=5}`) are always escaped. Declarations in `$padSelect` are PHP and taken as written.
On every other data - a `{array}` query, JSON, page PHP - `where=` and `group=` are the handling
options instead: a PAD expression per row, and grouping with subtotals in the template (see
[HANDLING.md](reference/HANDLING.md#group)).

### Nested Relations (Automatic Joins)

When a table tag is nested inside another, PAD automatically uses the defined relation:

```html
{forum_topics $id=$id}
  <h1>{$title}</h1>

  <!-- Gets the topic's author via user_id relation -->
  {users}
    Posted by {$username}
  {/users}

  <!-- Gets posts for this topic via topic_id relation -->
  {forum_posts}
    <div class="post">
      {$content}
      <!-- Gets the post's author -->
      {users}
        by {$username}
      {/users}
    </div>
  {/forum_posts}
{/forum_topics}
```

### Counting related rows

```html
{forum_boards}
  {$name}: {forum_topics fields='count(*) as n'}{$n}{/forum_topics} topics
{/forum_boards}
```

(`{count 'name'}` is no counter: it is a test whether a data store holds anything, and prints no
number.)

### Combining with {field} for Stats

```html
<div class="stats">
  {field "count(*) from users"} members
  {field "count(*) from forum_posts"} posts
</div>
```

### PHP Side - Minimal Code

With PAD Select, PHP files become minimal:

```php
// Before (traditional)
$topic = db("RECORD t.*, u.username FROM forum_topics t
             JOIN users u ON t.user_id = u.id WHERE t.id={0}", [$id]);

// After (PAD Select)
if (!db("CHECK forum_topics WHERE id = {0}", [$id]))
    padRedirect('forum/index');
$title = db("FIELD title FROM forum_topics WHERE id = {0}", [$id]);
// Template handles all the data fetching
```

### Key Benefits

1. **Declarative data access** - Template describes what data it needs
2. **Automatic joins** - Relations handle foreign key lookups
3. **Less PHP code** - No need to build arrays in PHP
4. **Cleaner separation** - PHP handles validation/actions, template handles display

---

## Database Configuration

In `_config/config.php`:

```php
// Database connection
$padSqlHost     = 'localhost';
$padSqlDatabase = 'myapp';
$padSqlUser     = 'user';
$padSqlPassword = 'pass';
```

### SQLite - no database server

`$padSqlDriver` chooses the application database's driver: `'mysql'` (the default, mysqli on
the settings above) or `'sqlite'` (PDO). For SQLite `$padSqlDatabase` is the database file -
a relative name lives under `DATA/` - and `$padSqlSetup` an optional `.sql` file, relative to
the application, that builds the database the first time, when the file does not exist yet:

```php
$padSqlDriver   = 'sqlite';
$padSqlDatabase = 'myapp/myapp.sqlite';     // DATA/myapp/myapp.sqlite
$padSqlSetup    = '_install/schema.sql';    // CREATE TABLE ... INSERT ... - run once
```

Everything above works alike on both: the `db()` verbs and their result shapes (`field`,
`record`, `array`, `check`, `insert` answering the new id, `update`/`delete` the rows
touched), the `{0}` placeholders, the database tags, named `_data/*.sql` queries and the
Select subsystem. The placeholders escape the way the driver needs - SQLite doubles a quote
and has no backslash escape, so a value can never end its literal early. The build runs
under a lock into a file of its own that is renamed into place, so a second request never
sees a half-built database. The SQL itself is the database's own dialect: `||` instead of
`concat()`, `AUTOINCREMENT`, no `TRUNCATE`. PAD's own database (`padDb()`, sessions, the `db`
page cache) stays MySQL. The `regression/sqlite` application runs on it.

---

## Database Library Functions

| Function | Description |
|----------|-------------|
| `db($sql, $vars)` | Execute SQL on application database |
| `padDb($sql, $vars)` | Execute SQL on PAD database |
| `padDbConnect($host, $user, $pass, $db)` | Create a MySQL connection |
| `padDbSqlite($file, $setup)` | Open (and on first use build) an SQLite database through PDO |

---

## Critical Syntax Notes

1. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`
2. **Placeholders quote themselves** - `WHERE name={0}` and `WHERE name='{0}'` are both safe; only `{x…}` keys are raw
3. **Use placeholders** - Use `{0}`, `{1}`, etc. for parameter substitution
4. **RECORD vs ARRAY** - RECORD returns one row, ARRAY returns all rows
