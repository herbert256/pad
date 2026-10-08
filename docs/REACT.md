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
└── _providers/           # PHP data providers
    ├── topic.php         # Returns topic record
    ├── user.php          # Returns user record
    └── posts.php         # Returns posts array
```

### The {reactData} Tag

`{reactData}` is a built-in tag (`pad/tags/reactData.php`) - an application writes no tag of
its own for it, and a `_tags/reactData.php` would replace the built-in one, since application
tags are found first. It runs `_providers/<provider>.php` (`provider=` defaults to `id=`),
keeps the result in `$padProviders[<id>]` for the providers after it, and writes
`<div id="<id>" data="<JSON>">` with the JSON escaped for the attribute. `type='check'` turns
the result into 1 or 0. The engine's `$padData` is not a name a provider or tag may assign.

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
<script type="text/babel" src="/react/examples/topic.js"></script>
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

**www/react/examples/topic.js:**
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

The page's first answer can go into the props with `{^orders}`, so the component shows data
at once and asks again only when the visitor changes something - the same PHP serves both.

---

## Pattern 3: Islands and a small runtime

The react application (`apps/react/`, `www/react/pad-react.js`) mounts its components on
*islands*: an element naming the component, its props as JSON in `data-props`, and HTML of
PAD's own inside that shows until React takes the element over.

```html
<!-- props.pad - one island per row of a PAD loop -->
{members}
  <div data-island="MemberCard" data-props="{^card}">
    <strong>{$name}</strong>
  </div>
{/members}
```

```javascript
// www/react/examples/props.js
function MemberCard({ name, role, skills }) { ... }

PadReact.island('MemberCard', MemberCard);   // every [data-island=MemberCard], its own root
```

Each island is its own React root with its own state. The runtime passes what PAD rendered
inside as `props.serverHtml` - a component can keep it, as the chart example keeps the SVG.

---

## Talking back to PAD

**Posts with the CSRF token.** With `$padCsrf` on, a post without the session's token is
answered 403 before the page runs. Write the token where a script can read it and send it in
the `X-CSRF-Token` header; the page validates as for any form and answers JSON.

```html
<meta name="csrf-token" content="{csrf token}">
```

```php
// feedback.php
if ( padRequestIs ( 'POST' ) )
  $errors = padValidate ( [ 'email' => 'required|email', 'message' => 'required|min:10' ] );

$padExpose = [ 'errors', 'wall' ];
```

```javascript
fetch('?feedback&padFormat=json', {
  method: 'POST',
  headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
  body: new URLSearchParams(form)
});
```

**One rendered part.** `{fragment 'chart'}...{/fragment}` in a template is answered alone to
`?page&padFragment=chart` - a component can let PAD render what it does well (a `{chart}`, a
`{markdown}` text, a table) and put the HTML in place.

**State on the server.** Keep what several islands share in the session (`padSession`,
`padSessionPut`) and let every change answer the whole state; one island tells the others
with an event on `window`. A reload starts every island from the session again.

**Polling.** A component can ask a page's JSON on an interval - stop it while
`document.hidden` is true, so a forgotten tab does not keep the server busy.

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
<div data-config="{echo $configJson | html | ignore}"></div>
```

`html` escapes the JSON's quotes for the attribute - `{echo}` does not - and `ignore` keeps PAD
off the braces. For an array field `data-config="{^config}"` does both:

```html
<div data-config="{^config}"></div>
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
│   └── json.php              # {json} tag for static data - {reactData} is built in
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
