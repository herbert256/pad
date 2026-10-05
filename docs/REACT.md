# REACT.md - PAD + React Integration

This file documents patterns for integrating React with PAD templates.

---

## Overview

When integrating React with PAD, the key principle is **separation of concerns**:
- **PAD** handles server-side data preparation and HTML structure
- **React** handles client-side interactivity

---

## Pattern 1: Static Data with {json} Tag

Use this pattern for data stored in JSON files in `_data/`.

### Data-Driven Navigation

**_data/nav.json:**
```json
[
  { "page": "index", "label": "Home", "icon": "🏠" },
  { "page": "about", "label": "About", "icon": "📖" }
]
```

**_inits.pad:**
```html
<nav>
  {local:nav.json}
    <a href="?{$page}" {if $padPage == $page}class="active"{/if}>
      {$icon} {$label}
    </a>
  {/local:nav.json}
</nav>
```

### Custom {json} Tag

**_tags/json.php:**
```php
<?php
  // Read JSON file from _data/, compact and HTML-escape for attributes
  $jsonContent = file_get_contents(APP . "_data/$padParm.json");
  $jsonData = json_decode($jsonContent, true);
  $jsonCompact = json_encode($jsonData);
  $padContent = htmlspecialchars($jsonCompact, ENT_QUOTES, 'UTF-8');
  return TRUE;
?>
```

### Passing JSON to React

**template.pad:**
```html
<div id="app" data-products="{json 'products' | ignore}"></div>

{ignore}<script>
  const products = JSON.parse(document.getElementById('app').dataset.products);
  // React can now use products
</script>{/ignore}
```

---

## Pattern 1b: A Field as JSON with {^name}

When the data is already a field of the page - set in the page's `.php`, or a field of the
row a loop is on - the `^` sigil writes it as JSON, escaped for a double-quoted attribute,
with no tag of your own and no `| ignore`:

**product.php:**
```php
<?php
  $product = [ 'name' => 'Chair', 'price' => 49.5, 'tags' => [ 'wood', 'brown' ] ];
?>
```

**product.pad:**
```html
<div id="product" data-props="{^product}"></div>
```

An array field is encoded whole; a scalar becomes a JSON string or number. Read it in the
component with `JSON.parse(elem.getAttribute('data-props'))`.

---

## Pattern 2: Dynamic Data with {reactData} Tag and Providers

Use this pattern for database-driven or dynamic data.

### Application Structure

```
apps/myapp/
├── _providers/           # PHP data providers
│   ├── topic.php         # Returns topic record
│   ├── user.php          # Returns user record
│   └── posts.php         # Returns posts array
└── _tags/
    └── reactData.php     # Custom tag implementation
```

### The {reactData} Tag

**_tags/reactData.php:**
```php
<?php
  // Get tag parameters
  $padId = padTagParm('id', '');           // HTML element ID
  $padProvider = padTagParm('provider', ''); // Provider name

  // Execute provider to get data
  $padProviderFile = APP . "_providers/$padProvider.php";
  if (file_exists($padProviderFile)) {
    $padData = include $padProviderFile;
  } else {
    padError("Provider not found: $padProvider");
  }

  // Convert to JSON and HTML-escape for attribute
  $padJson = json_encode($padData);
  $padJsonEscaped = htmlspecialchars($padJson, ENT_QUOTES, 'UTF-8');

  // Generate HTML div with data attribute
  $padContent = "<div id=\"$padId\" data=\"$padJsonEscaped\"></div>";

  return TRUE;
?>
```

### Provider Files

**_providers/topic.php** (single record):
```php
<?php
  // Providers return data - they have access to all variables from the page
  return db("RECORD * FROM forum_topics WHERE id={0}", [$id]);
?>
```

**_providers/posts.php** (array):
```php
<?php
  // IMPORTANT: Use array_values() to ensure proper JSON array (not object with numeric keys)
  $posts = db("ARRAY * FROM forum_posts WHERE topic_id={0}", [$id]);
  return array_values($posts);
?>
```

### Using {reactData} in Templates

**topic.pad:**
```html
<h1>Forum Topic</h1>

<!-- Multiple data sources - each with unique ID -->
{reactData id='topic', provider='topic', $id=$id}
{reactData id='board', provider='board', $boardId=$boardId}
{reactData id='user', provider='user', $userId=$userId}
{reactData id='posts', provider='posts', $id=$id}

<div id="react-app"></div>
<script type="text/babel" src="/react/topic/display.js"></script>
```

---

## Accessing Data in React

### CRITICAL: Use getAttribute(), NOT dataset

```javascript
// ❌ WRONG - dataset only works for data-* attributes, returns undefined for plain "data"
const topicElem = document.getElementById('topic');
const topic = JSON.parse(topicElem.dataset.data);  // FAILS!

// ✅ CORRECT - Use getAttribute() for plain "data" attribute
const topicElem = document.getElementById('topic');
const topic = JSON.parse(topicElem.getAttribute('data'));  // WORKS!
```

### Complete React Component Example

**www/react/topic/display.js:**
```javascript
function TopicDisplay() {
  // Get data from all reactData divs
  // IMPORTANT: Use getAttribute('data') NOT dataset.data!
  const topicElem = document.getElementById('topic');
  const boardElem = document.getElementById('board');
  const userElem = document.getElementById('user');
  const postsElem = document.getElementById('posts');

  const topic = JSON.parse(topicElem.getAttribute('data'));
  const board = JSON.parse(boardElem.getAttribute('data'));
  const user = JSON.parse(userElem.getAttribute('data'));
  const posts = JSON.parse(postsElem.getAttribute('data'));

  return (
    <div className="topic-display">
      <div className="breadcrumb">
        <a href="?forum/index">Forum</a> →
        <a href={`?forum/board&id=${board.id}`}>{board.name}</a>
      </div>

      <h1>{topic.title}</h1>
      <div className="topic-meta">
        Posted by {user.username} on {new Date(topic.created_at).toLocaleDateString()}
      </div>

      <div className="posts">
        {posts.map((post, index) => (
          <div key={post.id} className="post">
            <div className="post-header">
              Post #{index + 1} by {post.username}
            </div>
            <div className="post-content">
              {post.content}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

// Render the component
const root = ReactDOM.createRoot(document.getElementById('react-app'));
root.render(<TopicDisplay />);
```

---

## Fetching from ordinary pages

A component that fetches its data after the page loaded does not need a provider: any page
answers its data as JSON when it names what may leave the server in `$padExpose`.

```php
// orders.php - the same page renders orders.pad for a browser
$orders    = db ( "ARRAY * FROM orders" );
$padExpose = [ 'orders' ];
```

```javascript
fetch('?orders&padFormat=json').then(r => r.json()).then(data => setOrders(data.orders));
// or: fetch('?orders', { headers: { Accept: 'application/json' } })
```

---

## Key Principles

| Principle | Description |
|-----------|-------------|
| Server-side data | Use `_data/*.json` files (static) or `_providers/*.php` (dynamic) |
| PAD responsibility | Data preparation and HTML structure |
| React responsibility | Client-side interactivity |
| JSON in attributes | Use `\| ignore` pipe with {json} tag |
| JavaScript blocks | Wrap in `{ignore}...{/ignore}` tags |
| Data attribute access | Use `getAttribute('data')` NOT `dataset.data` |
| JSON arrays | Use `array_values()` in providers for proper arrays |
| Unique IDs | Each {reactData} needs unique `id` parameter |
| Variable access | Providers have access to all page variables |

---

## Handling Curly Braces

PAD parses `{ }` as tags. When working with JavaScript/React:

### Use {ignore} Tags

```html
{ignore}
<script>
  const user = { name: 'Alice', role: 'Developer' };
  if (user.active) { console.log('Active'); }
</script>
{/ignore}
```

### Use External Files (Preferred)

```html
<script src="/js/app.js"></script>
<script type="text/babel" src="/react/components/MyComponent.js"></script>
```

### Use | ignore Pipe

```html
<div data-config="{echo $configJson | ignore}"></div>
```

---

## CSS with React

Same principle applies - prefer external CSS files:

```html
<link rel="stylesheet" href="/css/react-components.css">
```

Or use {ignore} for inline styles:

```html
{ignore}
<style>
  .topic-display { padding: 20px; }
  .post { margin: 10px 0; }
</style>
{/ignore}
```

---

## File Organization

Recommended structure for React integration:

```
apps/myapp/
├── _data/                    # Static JSON data
│   ├── nav.json
│   └── config.json
├── _providers/               # Dynamic data providers
│   ├── user.php
│   └── posts.php
├── _tags/
│   ├── json.php              # {json} tag for static data
│   └── reactData.php         # {reactData} tag for dynamic data
└── pages/
    └── forum/
        └── topic.pad

www/
├── react/                    # React components
│   └── topic/
│       └── display.js
├── js/                       # Plain JavaScript
└── css/                      # Stylesheets
```

---

## Common Patterns

### Loading State

```javascript
function MyComponent() {
  const dataElem = document.getElementById('my-data');

  if (!dataElem) {
    return <div>Loading...</div>;
  }

  const data = JSON.parse(dataElem.getAttribute('data'));

  return <div>{/* render data */}</div>;
}
```

### Error Handling

```javascript
function MyComponent() {
  const dataElem = document.getElementById('my-data');

  try {
    const data = JSON.parse(dataElem.getAttribute('data'));
    return <div>{data.title}</div>;
  } catch (e) {
    return <div>Error loading data</div>;
  }
}
```

### Multiple Data Sources

```html
{reactData id='users', provider='users'}
{reactData id='roles', provider='roles'}
{reactData id='permissions', provider='permissions'}

<div id="admin-panel"></div>
```

```javascript
function AdminPanel() {
  const users = JSON.parse(document.getElementById('users').getAttribute('data'));
  const roles = JSON.parse(document.getElementById('roles').getAttribute('data'));
  const permissions = JSON.parse(document.getElementById('permissions').getAttribute('data'));

  // Combine data as needed
  return <div>{/* admin UI */}</div>;
}
```
