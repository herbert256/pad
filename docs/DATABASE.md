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

**Important:** PAD does NOT add quotes - you must quote string placeholders:
```php
db("SELECT * FROM users WHERE name='{0}'", [$name]);  // Correct
db("SELECT * FROM users WHERE name={0}", [$name]);    // WRONG for strings
```

---

## Template Database Tags

### {field} - Query Single Value

```
{field "count(*) from users"}
{field "name from users where id=5"}
```

### {table} - Query and Iterate Rows

```
{table "SELECT * FROM users"}
  <tr><td>{$name}</td><td>{$email}</td></tr>
{/table}
```

### {record} - Query Single Record

```
{record "SELECT * FROM users WHERE id=5"}
  Name: {$name}, Email: {$email}
{/record}
```

### {array} - Query as Array

```
{array 'users'}
  {$name}
{/array}
```

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
```

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
2. **Quote strings** - Always quote string placeholders: `WHERE name='{0}'`
3. **Use placeholders** - Use `{0}`, `{1}`, etc. for parameter substitution
4. **RECORD vs ARRAY** - RECORD returns one row, ARRAY returns all rows
