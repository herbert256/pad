# Support Application

A full-featured support portal demonstrating a real-world PAD application with multiple modules.

## Modules

| Module | Description |
|--------|-------------|
| Forum | Discussion boards with topics and posts |
| News | News articles with creation and viewing |
| Tickets | Support ticket system (requires authentication) |
| Auth | User authentication (login, register, logout, profile) |

## Structure

```
support/
├── index.pad                 # Home page
├── _inits.pad                # Global layout wrapper
├── _lib/auth.php             # Authentication library
├── _include/nav.pad          # Navigation component
├── _data/menu.json           # Menu configuration
├── _config/config.php        # Application configuration
├── _install/support.sql      # Database schema
│
├── auth/                     # Authentication module
│   ├── login.php / .pad
│   ├── register.php / .pad
│   ├── logout.php / .pad
│   └── profile.php / .pad
│
├── forum/                    # Forum module
│   ├── index.php / .pad      # Board listing
│   ├── board.php / .pad      # Topic listing
│   ├── topic.php / .pad      # Post listing
│   └── new.php / .pad        # New topic/post
│
├── news/                     # News module
│   ├── index.php / .pad      # Article listing
│   ├── view.php / .pad       # Article view
│   └── new.php / .pad        # New article
│
└── tickets/                  # Ticket module
    ├── index.pad             # Ticket listing
    └── new.php / .pad        # New ticket
```

## Features Demonstrated

- **Multi-module structure**: Subdirectories for different sections
- **Authentication**: User login/logout with session handling
- **Database integration**: Full CRUD operations
- **Navigation**: JSON-driven menu with auth-based visibility
- **Reusable components**: `_include/nav.pad` for shared navigation

## Database

Import the schema from `_install/support.sql` before use.

## Access

Via web browser: `http://server/support/`
