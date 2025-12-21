# PAD + React Client Application

This application demonstrates how to combine **PAD** (server-side template engine) with **React** (client-side JavaScript framework).

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

### Server-Side (PAD)

```
apps/react/
├── _inits.pad          # Global wrapper - loads React on every page
├── _inits.php          # Global PHP initialization
├── _config/
│   └── config.php      # Application configuration
├── _data/
│   └── nav.json        # Navigation menu data
├── index.php/pad       # Home page (data + template)
├── examples.php/pad    # Examples page
├── components.php/pad  # Components page
└── counter.php/pad     # Counter demo
```

### Client-Side (React)

React is loaded via CDN in `_inits.pad`:
- React 18 (Development build)
- ReactDOM 18 (Development build)
- Babel Standalone (for in-browser JSX transformation)

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

### 3. React Components in Templates

Embed React components directly in PAD templates:

```html
<!-- Container for React component -->
<div id="my-component"></div>

<!-- React component definition -->
<script type="text/babel">
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
</script>
```

### 4. Passing Server Data to React

Use data attributes to pass PAD/PHP data to React:

```html
<!-- In .php file -->
<?php
  $users = [
    ['name' => 'Alice', 'role' => 'Developer'],
    ['name' => 'Bob', 'role' => 'Designer'],
  ];
?>

<!-- In .pad file -->
<div id="users-app" data-users='{echo padJson($users)}'></div>

<script type="text/babel">
  function UsersApp() {
    const element = document.getElementById('users-app');
    const users = JSON.parse(element.dataset.users);

    return (
      <ul>
        {users.map((user, i) => (
          <li key={i}>{user.name} - {user.role}</li>
        ))}
      </ul>
    );
  }

  ReactDOM.createRoot(document.getElementById('users-app')).render(<UsersApp />);
</script>
```

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

**Important:** When embedding React/JSX code in PAD templates, wrap script blocks in `{ignore}...{/ignore}` tags to prevent PAD from parsing JSX curly braces as PAD syntax:

```html
{ignore}<script type="text/babel">
  function MyComponent() {
    return <div>{count}</div>;  <!-- JSX braces won't conflict with PAD -->
  }
</script>{/ignore}
```

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

1. Create `mypage.php` (optional - for server data):
```php
<?php
  $title = 'My Page';
  $data = ['item1', 'item2', 'item3'];
?>
```

2. Create `mypage.pad`:
```html
<h1>My Page</h1>

<div id="my-react-component"></div>

<script type="text/babel">
  function MyComponent() {
    return <div>Hello from React!</div>;
  }

  ReactDOM.createRoot(document.getElementById('my-react-component'))
    .render(<MyComponent />);
</script>
```

3. Access at `http://localhost/react/?mypage`

## Resources

- [React Documentation](https://react.dev)
- [React Tutorial](https://react.dev/learn/tutorial-tic-tac-toe)
- [PAD Framework Documentation](../../README.md)

## Tips

- React is automatically available on every page via `_inits.pad`
- Use `type="text/babel"` for JSX script tags
- **Wrap JSX scripts in `{ignore}...{/ignore}`** to prevent PAD from parsing React curly braces
- Pass server data via `data-*` attributes or `window` object
- Each component needs its own root element (`<div id="...">`)
- Use PAD for routing and server data, React for interactive UI
- Check browser console for React errors and warnings
- **Follow the PAD way**: Store repeating data in `_data/` JSON files (navigation, config, lists)
- Use `{local:filename.json}` to iterate over data files
- Keep templates focused on presentation, data in separate files

## Next Steps

- Learn React hooks: `useState`, `useEffect`, `useContext`
- Explore React Router for client-side routing
- Try integrating with external APIs
- Build a full application combining PAD and React
- Consider moving to a build tool for production use
