# PAD Support Portal

A modern full-featured support portal built with PAD and React, demonstrating complete separation of concerns.

## Features

- **Forum System**: Discussion boards, topics, and posts
- **News Module**: Article publishing and viewing
- **Ticket System**: Bug reports, feature requests, and questions
- **User Authentication**: Login, register, profile management
- **Admin Features**: User management, ticket status updates
- **Modern UI**: React components with animated stats dashboard
- **Responsive Design**: Mobile-friendly interface

## Architecture

This application follows PAD's separation of concerns philosophy extended to client-side code:

### Server-Side (PAD)

```
apps/support/
├── _config/
│   └── config.php          # Database and app configuration
├── _data/
│   └── nav.json            # Navigation menu data
├── _lib/
│   ├── auth.php            # Authentication functions
│   └── select.php          # PAD Select table definitions
├── _tags/
│   └── json.php            # Custom {json} tag for data passing
├── _install/
│   └── support.sql         # Database schema
├── _inits.php              # Session management and title setup
├── _inits.pad              # Global layout wrapper with React CDN
│
├── index.php / .pad        # Home page with stats dashboard
│
├── auth/                   # Authentication module
│   ├── login.php / .pad
│   ├── register.php / .pad
│   ├── logout.php / .pad
│   └── profile.php / .pad
│
├── forum/                  # Forum module
│   ├── index.php / .pad    # Board listing
│   ├── board.php / .pad    # Topic listing
│   ├── topic.php / .pad    # Post listing
│   └── new.php / .pad      # New topic/post
│
├── news/                   # News module
│   ├── index.php / .pad    # Article listing
│   ├── view.php / .pad     # Article view
│   └── new.php / .pad      # New article (admin only)
│
└── tickets/                # Ticket module
    ├── index.php / .pad    # Ticket listing
    ├── view.php / .pad     # Ticket details and comments
    └── new.php / .pad      # New ticket
```

### Client-Side (React)

```
www/support/
├── index.php               # PAD entry point
├── style.css               # Modern stylesheet
│
├── index/                  # Home page JavaScript
│   ├── nav.js              # Navigation component
│   ├── stats.js            # Animated stats dashboard
│   ├── news.js             # News preview
│   ├── boards.js           # Forum boards preview
│   └── links.js            # Quick links
│
├── auth/                   # Authentication JavaScript
│   ├── login.js            # Login form
│   ├── register.js         # Register form with validation
│   └── profile.js          # Profile display and stats
│
├── forum/                  # Forum JavaScript
│   ├── boards.js           # Boards listing
│   ├── topics.js           # Topics listing
│   ├── posts.js            # Posts display and reply form
│   └── new.js              # New topic/post form
│
├── news/                   # News JavaScript
│   ├── list.js             # News articles listing
│   ├── article.js          # Article header
│   └── new.js              # New article form
│
└── tickets/                # Tickets JavaScript
    ├── list.js             # Tickets listing with filtering
    ├── details.js          # Ticket details and admin actions
    ├── comments.js         # Comments display and form
    └── new.js              # New ticket form
```

## Separation of Concerns

### 1. Data Layer (_data/)
- **nav.json**: Navigation menu structure with visibility rules

### 2. Server Logic (.php files)
- Database queries
- Authentication checks
- Form processing
- Data validation
- Session management
- Redirects

### 3. Templates (.pad files)
- HTML structure
- Server data binding via data attributes
- Script references to external JavaScript
- PAD Select for database iteration

### 4. Client Interactivity (.js files)
- React components
- Form validation
- Dynamic UI updates
- User interactions
- Animations

### 5. Custom Tags (_tags/)
- **json.php**: Reads JSON files from `_data/`, compacts and HTML-escapes for safe use in attributes

### 6. Authentication (_lib/auth.php)
- `isLoggedIn()`: Check if user is logged in
- `isAdmin()`: Check if user is admin
- `requireLogin()`: Redirect if not logged in
- `requireAdmin()`: Redirect if not admin
- `hashPassword()`: Hash passwords securely
- `verifyPassword()`: Verify password against hash
- `getCurrentUser()`: Get current user data

### 7. PAD Select (_lib/select.php)
Defines database tables and relations for template-driven data access:

**Tables:**
- users, forum_boards, forum_topics, forum_posts
- news, tickets, ticket_comments

**Virtual Tables:**
- openBugs: Open bug tickets sorted by update
- pendingTickets: Open/in-progress tickets sorted by priority

## Database Schema

Import `_install/support.sql` before use. The schema includes:

- **users**: User accounts with role (user/admin)
- **forum_boards**: Discussion boards
- **forum_topics**: Forum topics with views and pinned/locked flags
- **forum_posts**: Forum posts linked to topics and users
- **news**: News articles
- **tickets**: Support tickets (bug/feature/question)
- **ticket_comments**: Comments on tickets

## Installation

1. **Import Database**
   ```bash
   mysql -u root -p < apps/support/_install/support.sql
   ```

2. **Configure Database** (already done in `_config/config.php`)
   - Host: 127.0.0.1
   - Database: support
   - User: support
   - Password: support

3. **Access Application**
   ```
   http://localhost/support/
   ```

4. **Default Admin Account**
   - Username: admin
   - Password: password
   - Email: admin@pad.local

## Key Patterns

### Passing Data to React

**Server → Template → React:**
```html
<!-- index.pad -->
<div id="stats-dashboard"
     data-user-count="{$userCount}"
     data-topic-count="{$topicCount}"></div>
<script type="text/babel" src="/support/index/stats.js"></script>
```

**React reads data attributes:**
```javascript
// stats.js
const elem = document.getElementById('stats-dashboard');
const stats = {
  users: elem.dataset.userCount,
  topics: elem.dataset.topicCount
};
```

### Custom {json} Tag

**Usage:**
```html
<div data-items="{json 'filename' | ignore}"></div>
```

**How it works:**
1. Reads `_data/filename.json`
2. Compacts JSON (removes whitespace)
3. HTML-escapes for safe attribute use
4. `| ignore` prevents PAD from parsing JSON `{}`

### Authentication Flow

**Login:**
1. User submits form → `auth/login.php`
2. PHP validates credentials
3. Sets session variables
4. Redirects to home page

**Protected Pages:**
```php
<?php
  requireLogin();  // Redirect if not logged in
?>
```

### Forum Nesting with PAD Select

```html
{forum_topics $id=$id}
  <h1>{$title}</h1>
  
  <!-- Automatic join via relation -->
  {users}
    Posted by {$username}
  {/users}
  
  <!-- Posts for this topic -->
  {forum_posts}
    <div>{$content}</div>
    {users}by {$username}{/users}
  {/forum_posts}
{/forum_topics}
```

## Module Overview

### Home (`index`)
- Animated stats dashboard
- Latest news preview
- Forum boards preview
- Quick links (contextual based on login status)

### Forum (`forum/`)
- **index**: List all discussion boards
- **board**: List topics in a board
- **topic**: View topic posts and replies
- **new**: Create new topic or reply

### News (`news/`)
- **index**: List all articles
- **view**: Read full article
- **new**: Create article (admin only)

### Tickets (`tickets/`)
- **index**: List user's tickets (or all for admin)
- **view**: Ticket details with comments
- **new**: Create new ticket
- **Filtering**: Open/closed status
- **Admin**: Update ticket status

### Auth (`auth/`)
- **login**: User authentication
- **register**: New user registration with validation
- **profile**: User profile with activity stats
- **logout**: Session destruction

## React Components

All React components are external JavaScript files following PAD separation:

**Interactive Features:**
- Form validation (register, tickets, forum)
- Animated statistics
- Dynamic filtering (tickets)
- Real-time password matching
- Status badges and icons
- Responsive navigation
- Profile statistics

**Data Flow:**
1. PHP prepares data
2. PAD template renders data attributes
3. React reads attributes and renders components
4. User interactions handled by React
5. Form submissions processed by PHP

## Benefits of This Architecture

1. **Separation of Concerns**
   - Data in JSON files
   - Logic in PHP
   - Structure in PAD templates
   - Interactivity in JavaScript

2. **Maintainability**
   - Each layer is independent
   - Easy to update without affecting others
   - Clear file organization

3. **Performance**
   - External JavaScript is cacheable
   - Server renders data efficiently
   - React provides smooth UI

4. **Scalability**
   - Add new modules easily
   - Extend existing features
   - Database-driven content

5. **Developer Experience**
   - Clear structure
   - Easy debugging
   - Reusable components

## Creating New Pages

Follow this 5-step pattern:

1. **Create Data** (Optional): `_data/mydata.json`
2. **Create PHP**: `mypage.php` (server logic and data)
3. **Create Template**: `mypage.pad` (HTML structure)
4. **Create JavaScript**: `www/support/mypage/component.js`
5. **Access**: `http://localhost/support/?mypage`

## Tips

- **Data**: Store static data in `_data/*.json`
- **PHP**: Only server-side logic (DB, auth, validation)
- **Templates**: Structure only, no inline JavaScript
- **JavaScript**: All interactivity in external files
- **Authentication**: Use auth functions from `_lib/auth.php`
- **Database**: Use PAD Select for template-driven queries
- **Forms**: POST to same page, process in PHP section
- **Redirects**: Use `padRedirect()` not `header()` + `exit`

## License

Part of the PAD Framework - GPL v3
