# Test Application

A testing application for PAD's database table subsystem.

## Purpose

This application tests the PAD Select subsystem which provides automatic table querying based on defined table schemas and relationships.

## Files

| File | Description |
|------|-------------|
| `index.pad` | Test template querying users table |
| `_lib/select.php` | Table and relationship definitions |
| `_config/config.php` | Database configuration |

## Table Definitions

The `_lib/select.php` file defines:

**Tables:**
- `users`
- `forum_boards`
- `forum_topics`
- `forum_posts`
- `news`
- `tickets`
- `ticket_comments`

**Relationships:**
- `forum_topics` → `forum_boards` (via `board_id`)
- `forum_topics` → `users` (via `user_id`)
- `forum_posts` → `forum_topics` (via `topic_id`)
- `forum_posts` → `users` (via `user_id`)
- `news` → `users` (via `user_id`)
- `tickets` → `users` (via `user_id`)
- `ticket_comments` → `tickets` (via `ticket_id`)
- `ticket_comments` → `users` (via `user_id`)

## Example Usage

**index.pad**:
```
{users $id=2}
  {$email}
{/users}
```

This queries the `users` table where `id=2` and outputs the email field.

## Access

Via web browser: `http://server/test/`
