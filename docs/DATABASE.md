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
`id={0}` cannot take `5 or 1=1` as SQL. An array written bare becomes a list. Placeholders are
filled in one pass, so a value holding `{1}` stays as written. Keys starting with `x` are
inserted raw - a deliberate escape hatch for SQL the code builds itself, never for input.
```php
db("SELECT * FROM users WHERE name='{0}'", [$name]);      // escaped inside the quotes
db("SELECT * FROM users WHERE name={0}", [$name]);        // quoted for you
db("SELECT * FROM users WHERE id IN ({0})", [[1, 2, 3]]);  // a list
```

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
$padSelect ['forum_topics']  = [ 'key' => 'id' ];
$padSelect ['forum_posts']   = [ 'key' => 'id', 'order' => 'created_at' ];

// Define relations (foreign keys)
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

<!-- Filter by field -->
{forum_boards $slug=$slug}
  {$name}
{/forum_boards}

<!-- With options -->
{news sort="created_at desc" rows=10}
  {$title}
{/news}

<!-- A condition of your own: write it as a quoted string, put values in as $name -->
{customers where="country = $country and creditLimit > $minimum", order="$sortColumn"}
  {$customerName}
{/customers}
```

`where=`, `having=`, `order=` and `group=` written on a tag are SQL, so they must be quoted
strings in the template; `where=$cond` is refused. Inside them a `$name` is bound by the select:
in `where=`/`having=` as a quoted, escaped literal of that application variable (a number as a
number), in `order=`/`group=` as column names, each with an optional `asc`/`desc`, and nothing
else. `{$name}` there splices text into your SQL instead - use `$name`. Keys bound on the tag
(`{users $id=5}`) are always escaped. Declarations in `$padSelect` are PHP and taken as written.

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

### Counting with {count}

```html
{forum_boards}
  {$name}: {count 'forum_topics'} topics
{/forum_boards}
```

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

---

## Database Library Functions

| Function | Description |
|----------|-------------|
| `db($sql, $vars)` | Execute SQL on application database |
| `padDb($sql, $vars)` | Execute SQL on PAD database |
| `padDbConnect($host, $user, $pass, $db)` | Create database connection |

---

## Critical Syntax Notes

1. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`
2. **Placeholders quote themselves** - `WHERE name={0}` and `WHERE name='{0}'` are both safe; only `{x…}` keys are raw
3. **Use placeholders** - Use `{0}`, `{1}`, etc. for parameter substitution
4. **RECORD vs ARRAY** - RECORD returns one row, ARRAY returns all rows
