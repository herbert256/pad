# PAD + React Client Application

## Introduction

Demonstrates how to combine PAD (server-side template engine) with React (client-side JavaScript framework).

## Overview

- **PAD** handles: Routing, server-side data, templates, initial HTML
- **React** handles: Interactive UI components, client-side state, dynamic updates
- **React is loaded globally** in `_inits.pad` and available on every page

## Quick Start

Access the application at: `http://localhost/react/`

### URLs

- `?index` - Home page with introduction and examples
- `?examples` - Various React patterns and examples
- `?components` - Reusable component patterns
- `?counter` - Interactive counter demo

## Architecture

This application follows PAD's philosophy of **separation of concerns**, extended to client-side code:

### Server-Side (PAD)

```
apps/react/
├── _inits.pad          # Global wrapper - loads React on every page
├── _inits.php          # Global PHP initialization
├── _config/
│   └── config.php      # Application configuration
├── _data/              # Static data (JSON files)
│   ├── nav.json        # Navigation menu
│   ├── products.json   # Product data
│   └── users.json      # User data
├── _tags/              # Custom PAD tags
│   └── json.php        # {json} tag for data passing to React
├── index.php/pad       # Home page (data + template)
├── examples.php/pad    # Examples page
├── components.php/pad  # Components page
└── counter.php/pad     # Counter demo
```

### Client-Side (React)

**React libraries** loaded via CDN in `_inits.pad`:
- React 18 (Development build)
- ReactDOM 18 (Development build)
- Babel Standalone (for in-browser JSX transformation)

**JavaScript components** (external files in `www/react/`):
```
www/react/
├── index/              # Home page JavaScript
│   ├── welcome.js      # WelcomeComponent
│   └── users.js        # UsersComponent
├── examples/           # Examples page JavaScript
│   ├── click.js        # ClickExample
│   ├── form.js         # FormExample
│   ├── products.js     # ProductList
│   └── toggle.js       # ToggleExample
├── components/         # Components page JavaScript
│   ├── demo.js         # ComponentsDemo
│   └── todo.js         # TodoApp
└── counter/            # Counter page JavaScript
    └── app.js          # CounterApp
```

**Separation Benefits:**
- **Data** (_data/*.json) - Static data separate from code
- **PHP** (*.php) - Server logic and dynamic values
- **Templates** (*.pad) - HTML structure
- **JavaScript** (www/react/*/*.js) - Client-side interactivity
- Each concern in its proper place, clean and maintainable

## How It Works

### 1. Global React Loading

`_inits.pad` includes:
```html
<script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
```

### 2. Page Structure

Each page consists of:
- **`.php` file** - Server-side data (optional)
- **`.pad` file** - Template with React components

### 3. React Components in External Files

React components are defined in external JavaScript files and referenced from templates:

**Template (page.pad):**
```html
<!-- Container for React component -->
<div id="my-component"></div>

<!-- Load external component -->
<script type="text/babel" src="/react/page/component.js"></script>
```

**JavaScript (www/react/page/component.js):**
```javascript
function MyComponent() {
  const [count, setCount] = React.useState(0);

  return (
    <div>
      <p>Count: {count}</p>
      <button onClick={() => setCount(count + 1)}>
        Increment
      </button>
    </div>
  );
}

// Render the component
const root = ReactDOM.createRoot(document.getElementById('my-component'));
root.render(<MyComponent />);
```

**Why external files?**
- Consistent with PAD's separation philosophy
- Browser caching for better performance
- Cleaner templates (structure only)
- Easier debugging with proper file names
- Can be minified/optimized for production

### 4. Passing Server Data to React

The app uses a custom `{json}` tag to pass data from PAD to React cleanly and safely.

#### Custom {json} Tag

**Location:** `_tags/json.php`

**Purpose:** Reads JSON files from `_data/`, compacts them, and HTML-escapes for safe use in attributes.

**Implementation:**
```php
<?php
  $jsonContent = file_get_contents(APP . "_data/$padParm.json");
  $jsonData = json_decode($jsonContent, true);
  $jsonCompact = json_encode($jsonData);
  $padContent = htmlspecialchars($jsonCompact, ENT_QUOTES, 'UTF-8');
  return TRUE;
?>
```

#### Usage Pattern

**1. Create JSON data file** (_data/users.json):
```json
[
  {"id": 1, "name": "Alice", "role": "Developer"},
  {"id": 2, "name": "Bob", "role": "Designer"}
]
```

**2. Use in template** (page.pad):
```html
<div id="users-app" data-users="{json 'users' | ignore}"></div>
```

**3. Access in JavaScript** (www/react/page/users.js):
```javascript
function UsersApp() {
  const element = document.getElementById('users-app');
  const users = JSON.parse(element.dataset.users);

  return (
    <ul>
      {users.map(user => (
        <li key={user.id}>{user.name} - {user.role}</li>
      ))}
    </ul>
  );
}

ReactDOM.createRoot(document.getElementById('users-app')).render(<UsersApp />);
```

**Key points:**
- `{json 'filename'}` reads from `_data/filename.json`
- `| ignore` pipe prevents PAD from parsing JSON curly braces
- Data is HTML-escaped for safe use in attributes
- React parses JSON from `data-*` attributes

### 5. Data-Driven Navigation (The PAD Way)

Following PAD's philosophy of separating data from presentation, the navigation menu is driven by `_data/nav.json`:

**_data/nav.json:**
```json
[
  {
    "page": "index",
    "label": "Home",
    "icon": "🏠"
  },
  {
    "page": "examples",
    "label": "Examples",
    "icon": "📚"
  },
  {
    "page": "components",
    "label": "Components",
    "icon": "🧩"
  },
  {
    "page": "counter",
    "label": "Counter Demo",
    "icon": "🔢"
  }
]
```

**_inits.pad (navigation rendering):**
```html
<nav>
  {local:nav.json}
    <a href="?{$page}" {if $padPage == $page}class="active"{/if}>{$icon} {$label}</a>
  {/local:nav.json}
</nav>
```

**Benefits:**
- Add/remove menu items by editing JSON, not template code
- Change labels and icons without touching HTML
- Easy to extend with additional properties (tooltips, badges, permissions, etc.)
- Template remains clean and focused on presentation
- Data can be reused elsewhere (breadcrumbs, sitemap, etc.)

## Features Demonstrated

### Home Page (`?index`)
- Simple React component with state
- Passing server data to React components
- Event handling
- Data from PAD/PHP rendered by React

### Examples Page (`?examples`)
- Click handlers
- Form with controlled components
- Sorting and filtering data
- Toggle components
- Server data integration

### Components Page (`?components`)
- Reusable component patterns
- Component composition
- Props and children
- Todo list application

### Counter Demo (`?counter`)
- State management
- Multiple state variables
- History tracking
- Undo functionality
- Conditional rendering

## Development Notes

### Current Setup (Development)

This application uses:
- **React Development Builds** - Larger, with helpful warnings
- **Babel Standalone** - In-browser JSX transformation (slower)
- **CDN Loading** - No build step required

**Pros:**
- Easy to set up and learn
- No build tools needed
- Immediate changes (just refresh)

**Cons:**
- Slower performance
- Larger bundle sizes
- Not suitable for production

### Production Setup (Recommended)

For production applications, use:
- **Vite** or **Create React App** for build tooling
- **React Production Builds** - Optimized and minified
- **Pre-compiled JSX** - Faster runtime performance
- **Bundle optimization** - Code splitting and tree shaking

Example with Vite:
```bash
npm create vite@latest my-app -- --template react
npm install
npm run build
```

Then copy the built files to PAD and reference them in `_inits.pad`.

## Learning Path

1. **Start with Home** (`?index`) - Understand the basics
2. **Explore Examples** (`?examples`) - See common patterns
3. **Study Components** (`?components`) - Learn composition
4. **Try Counter Demo** (`?counter`) - Build something interactive
5. **Create your own pages** - Add new `.php` and `.pad` files

## Creating New Pages

Following the separation of concerns pattern:

### 1. Create Data (Optional)

**_data/mydata.json:**
```json
[
  {"id": 1, "name": "Item 1"},
  {"id": 2, "name": "Item 2"}
]
```

### 2. Create PHP File (Optional)

**mypage.php:**
```php
<?php
  $title = 'My Page';
  // Only dynamic server values here
  $serverTime = date('Y-m-d H:i:s');
?>
```

### 3. Create Template

**mypage.pad:**
```html
<h1>My Page</h1>

<!-- Container for React component -->
<div id="my-component" data-items="{json 'mydata' | ignore}"></div>

<!-- Load external JavaScript -->
<script type="text/babel" src="/react/mypage/component.js"></script>
```

### 4. Create JavaScript Component

**www/react/mypage/component.js:**
```javascript
function MyComponent() {
  const element = document.getElementById('my-component');
  const items = JSON.parse(element.dataset.items);

  return (
    <div>
      <h2>Hello from React!</h2>
      <ul>
        {items.map(item => (
          <li key={item.id}>{item.name}</li>
        ))}
      </ul>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('my-component'))
  .render(<MyComponent />);
```

### 5. Access

Visit: `http://localhost/react/?mypage`

**File organization:**
- Data → `apps/react/_data/mydata.json`
- PHP → `apps/react/mypage.php`
- Template → `apps/react/mypage.pad`
- JavaScript → `www/react/mypage/component.js`

## Resources

- [React Documentation](https://react.dev)
- [React Tutorial](https://react.dev/learn/tutorial-tic-tac-toe)
- [PAD Framework Documentation](../../README.md)

## Tips

### General
- React is automatically available on every page via `_inits.pad`
- Use `type="text/babel"` for external JSX script tags
- Each component needs its own root element (`<div id="...">`)
- Use PAD for routing and server data, React for interactive UI
- Check browser console for React errors and warnings

### Separation of Concerns (The PAD Way)
- **Data** → Store in `_data/*.json` files (not in PHP or templates)
- **PHP** → Only dynamic server values (time, session, calculations)
- **Templates** → Structure only (HTML + script references)
- **JavaScript** → External files in `www/react/[page]/`

### Data Passing
- Use the custom `{json 'filename' | ignore}` tag for JSON data
- Pass data via `data-*` attributes
- Parse in JavaScript with `JSON.parse(element.dataset.attrName)`
- The `| ignore` pipe is essential for JSON (prevents PAD from parsing `{}`)

### File Organization
- One subdirectory per page in `www/react/`
- Group related components in the same directory
- Name files descriptively (welcome.js, products.js, todo.js)

## Next Steps

- Learn React hooks: `useState`, `useEffect`, `useContext`
- Explore React Router for client-side routing
- Try integrating with external APIs
- Build a full application combining PAD and React
- Consider moving to a build tool for production use
