# React Data Providers

This directory contains data providers for the `{reactData}` tag. Providers fetch data from the database and make it available to React components in a clean, type-safe way.

## The {reactData} Tag

The `{reactData}` tag is a PAD tag that:
1. Executes a data provider (PHP or PAD file)
2. Converts the result to JSON with proper types
3. Outputs an HTML div with the data in a `data` attribute
4. Makes the data available to React components

## Syntax

```pad
{reactData id='element-id', provider='provider-name', $param1=value1, $param2=value2}
```

**Options:**
- `id` - Required. HTML element ID for the generated div
- `provider` - Required. Name of the provider file (without extension)
- `$param1, $param2, ...` - Optional parameters passed to the provider

## How It Works

### 1. Create a Provider

**File:** `_reactData/topic.php`

```php
<?php
  // Parameters are available as variables
  return db("RECORD * FROM forum_topics WHERE id={0}", [$id]);
?>
```

### 2. Use in Template

**File:** `forum/topic.pad`

```pad
{reactData id='topic-data', provider='topic', $id=$topicId}
```

### 3. Generated HTML

```html
<div id="topic-data" data="{&quot;id&quot;:1,&quot;title&quot;:&quot;Topic Title&quot;,...}"></div>
```

### 4. Read in React Component

**File:** `www/support/forum/topic.js`

```javascript
function TopicComponent() {
  const elem = document.getElementById('topic-data');
  const topic = JSON.parse(elem.dataset.data);
  
  return <div>{topic.title}</div>;
}
```

## Available Providers

### topic.php
Fetches a forum topic by ID.

**Parameters:**
- `$id` - Topic ID

**Usage:**
```pad
{reactData id='topic-1', provider='topic', $id=1}
```

### board.php
Fetches a forum board by slug.

**Parameters:**
- `$slug` - Board slug

**Usage:**
```pad
{reactData id='board-data', provider='board', $slug='installation'}
```

### user.php
Fetches user information (without password).

**Parameters:**
- `$id` - User ID

**Usage:**
```pad
{reactData id='user-profile', provider='user', $id=$user_id}
```

### posts.php
Fetches posts for a topic with user information.

**Parameters:**
- `$topic_id` - Topic ID

**Usage:**
```pad
{reactData id='posts-list', provider='posts', $topic_id=1}
```

### test.php
Test provider for development.

**Parameters:**
- `$id` - Optional ID
- `$name` - Optional name

**Usage:**
```pad
{reactData id='test-data', provider='test', $id=123, $name='John'}
```

## Creating New Providers

### PHP Provider

Create a file `_reactData/mydata.php`:

```php
<?php
  // Parameters from {reactData} are available as variables
  // Example: {reactData provider='mydata', $user_id=5}
  //          → $user_id variable is available here
  
  // Return data (array, object, or scalar)
  return db("RECORD * FROM mytable WHERE user_id={0}", [$user_id]);
?>
```

### PAD Provider (Advanced)

Create a file `_reactData/mydata.pad`:

```pad
{data 'result'}
{
  "status": "success",
  "items": [
    {items}{"id": {$id}, "name": "{$name}"}{if notLast@items},{/if}{/items}
  ]
}
{/data}
```

## Multiple Instances

You can use the same provider multiple times with different IDs:

```pad
{reactData id='topic-1', provider='topic', $id=1}
{reactData id='topic-2', provider='topic', $id=2}
{reactData id='topic-3', provider='topic', $id=3}
```

This generates:

```html
<div id="topic-1" data="{...}"></div>
<div id="topic-2" data="{...}"></div>
<div id="topic-3" data="{...}"></div>
```

## Type Handling

The `{reactData}` tag uses `JSON_NUMERIC_CHECK` flag, which ensures:

- Numeric strings become numbers: `"123"` → `123`
- Boolean strings stay as numbers: `"0"` → `0`, `"1"` → `1`
- Dates remain as strings: `"2025-12-21 14:00:00"`

**Example:**

```php
<?php
  return [
    'id' => '123',           // → 123 (number)
    'count' => '42',         // → 42 (number)
    'active' => '1',         // → 1 (number, truthy)
    'disabled' => '0',       // → 0 (number, falsy)
    'title' => 'Hello',      // → "Hello" (string)
    'created_at' => '2025-12-21'  // → "2025-12-21" (string)
  ];
?>
```

## Benefits

1. **Separation of Concerns**
   - Data fetching logic in `_reactData/`
   - Templates remain clean
   - React components are generic

2. **Type Safety**
   - Numbers are numbers (not strings)
   - Booleans work correctly in conditionals
   - No manual type conversion needed

3. **Reusability**
   - Same provider can be used multiple times
   - Same React component can consume different data
   - DRY principle

4. **Maintainability**
   - Database queries in one place
   - Easy to update data structure
   - Clear data flow

## Example: Complete Flow

**1. Provider** (`_reactData/user.php`):
```php
<?php
  return db("RECORD id, username, email FROM users WHERE id={0}", [$id]);
?>
```

**2. Template** (`profile.pad`):
```pad
<h1>User Profile</h1>
{reactData id='user-info', provider='user', $id=$user_id}
<div id="profile-display"></div>
<script type="text/babel" src="/support/components/profile.js"></script>
```

**3. React Component** (`www/support/components/profile.js`):
```javascript
function ProfileComponent() {
  const elem = document.getElementById('user-info');
  const user = JSON.parse(elem.dataset.data);
  
  return (
    <div className="profile">
      <h2>{user.username}</h2>
      <p>Email: {user.email}</p>
      <p>User ID: {user.id}</p>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('profile-display'))
  .render(<ProfileComponent />);
```

**4. Generated HTML**:
```html
<h1>User Profile</h1>
<div id="user-info" data="{&quot;id&quot;:5,&quot;username&quot;:&quot;john&quot;,&quot;email&quot;:&quot;john@example.com&quot;}"></div>
<div id="profile-display"></div>
<script type="text/babel" src="/support/components/profile.js"></script>
```

**5. React Renders**:
```html
<div class="profile">
  <h2>john</h2>
  <p>Email: john@example.com</p>
  <p>User ID: 5</p>
</div>
```

## Best Practices

1. **Keep providers simple** - Just fetch and return data
2. **No business logic** - Providers should only retrieve data
3. **Use meaningful IDs** - `topic-header` is better than `div1`
4. **Document parameters** - Add comments to provider files
5. **Return arrays for lists** - Makes React mapping easier
6. **Use PAD Select** - Leverage existing table definitions when possible

## Troubleshooting

**Provider not found:**
- Check file exists in `_reactData/`
- Check spelling of provider name
- Extension can be `.php` or `.pad`

**Data is null in React:**
- Check browser console for JSON parse errors
- Verify provider returns valid data
- Check parameter names match

**Wrong data types:**
- Verify database query returns expected types
- Check if `JSON_NUMERIC_CHECK` is working
- Look at generated HTML source

**Multiple instances conflict:**
- Ensure each `{reactData}` has unique `id`
- IDs must be unique across the entire page
