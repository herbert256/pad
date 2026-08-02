# Demo Application

## Introduction

Interactive demo showcasing PAD framework features with practical examples.

## Examples

| Page | Description |
|------|-------------|
| Guestbook | A simple guestbook where visitors can leave messages |
| Todo List | A task manager to add, complete, and delete tasks |
| Contact Form | A contact form with validation |
| Page Counter | A visitor counter that tracks page views |
| Clock | Display current date and time using a custom tag |

## Structure

```
demo/
├── index.php / index.pad     # Home page with example list
├── guestbook.php / .pad      # Guestbook example
├── todo.php / .pad           # Todo list example
├── todoPost.php              # Todo form POST handler
├── contact.php / .pad        # Contact form example
├── counter.php / .pad        # Page counter example
├── clock.pad                 # Clock display
├── else.php                  # PHP-only page (no template)
├── _inits.php / .pad         # Global layout wrapper
├── _include/todo.pad         # Todo list snippet
├── _tags/clock.php           # Custom clock tag
└── _data/navigate.json       # Navigation menu data
```

## Features Demonstrated

- **Page pairing**: `.php` data files paired with `.pad` templates
- **Global wrapper**: `_inits.pad` provides consistent layout with `@page@` placeholder
- **Custom tags**: `_tags/clock.php` shows how to create application-specific tags
- **Data files**: `_data/navigate.json` demonstrates JSON data integration
- **Form handling**: Contact form shows POST processing
- **Data iteration**: Examples of iterating over arrays

## Access

Via web browser: `http://server/demo/`
