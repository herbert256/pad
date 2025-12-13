# PAD Database Directory

This directory contains SQL schema definitions and database setup scripts for the PAD framework's internal databases. These schemas support PAD's caching, tracking, and session management features.

## Overview

PAD uses a dedicated MySQL/MariaDB database (named `pad`) to store framework-level data including request tracking, session management, caching, and URL shortening. The SQL files in this directory define the complete database structure and can be used to initialize or reset the PAD database.

## Files

### SQL Schema Files

- **database.sql** - Database initialization script
  - Drops and recreates the `pad` database
  - Sets character set to UTF-8 (utf8mb4)
  - Creates the `pad` database user with password
  - Must be run first before other schema files

- **pad.sql** - Core PAD tables for tracking and session management
  - **track_session** - Session tracking table
    - Stores session identifiers and timing
    - Tracks session start/end times
    - Counts requests per session
  - **track_request** - Request tracking table
    - Records individual HTTP requests
    - Stores request metadata (URL, IP, user agent, referrer)
    - Tracks performance metrics (duration, bytes)
    - Links requests to sessions
  - **track_data** - Request data storage
    - Stores complete response data for tracked requests
    - Indexed by etag for efficient retrieval
  - **links** - URL shortening/FastLink table
    - Stores short link identifiers
    - Maps links to variable serializations
    - Enables clean URLs for complex parameter sets

- **cache.sql** - Caching tables for server-side cache storage
  - **cache_etag** - ETag metadata table
    - Stores etags with age timestamps
    - Enables cache expiration
  - **cache_url** - URL to ETag mapping
    - Maps URLs to their cached etags
    - Tracks cache age per URL
  - **cache_data** - Cached content storage
    - Stores actual cached data (potentially compressed)
    - Uses BLOB for binary data storage
    - Indexed by etag for fast retrieval

## Database Schema Details

### Track Session Table

```sql
CREATE TABLE `track_session` (
  `session` char(8) NOT NULL,           -- Session identifier
  `start` timestamp NOT NULL,            -- Session start time
  `end` timestamp NOT NULL,              -- Last activity time (auto-updated)
  `requests` int(11) NOT NULL DEFAULT 1  -- Request counter
) ENGINE=MyISAM;
```

Primary key: `session`

### Track Request Table

```sql
CREATE TABLE `track_request` (
  `session` char(8) NOT NULL,            -- Session identifier
  `request` char(8) NOT NULL,            -- Request identifier
  `page` varchar(32) NOT NULL,           -- PAD page name
  `time` TIMESTAMP NOT NULL,             -- Request timestamp
  `duration` bigint(11) NOT NULL,        -- Processing time (microseconds)
  `bytes` int(11) NOT NULL,              -- Response size
  `stop` varchar(3) NOT NULL,            -- Processing status
  `etag` char(22) NOT NULL,              -- Response ETag
  `url` varchar(1023) NOT NULL,          -- Request URL
  `ref` varchar(1023) NOT NULL,          -- HTTP referrer
  `ip` varchar(1023) NOT NULL,           -- Client IP address
  `agent` varchar(1023) NOT NULL         -- User agent string
) ENGINE=MyISAM;
```

Primary key: `session, request`

### Track Data Table

```sql
CREATE TABLE `track_data` (
  `etag` char(22) NOT NULL,              -- Unique content identifier
  `data` longtext NOT NULL               -- Complete response data
) ENGINE=MyISAM;
```

Primary key: `etag`

### Links Table

```sql
CREATE TABLE `links` (
  `link` varchar(64) NOT NULL,           -- Short link code
  `vars` text NOT NULL                   -- Serialized variables
) ENGINE=MyISAM;
```

Primary key: `link`

### Cache Tables

```sql
CREATE TABLE `cache_etag` (
  `etag` char(22) NOT NULL,              -- Content identifier
  `age` int NOT NULL                     -- Cache timestamp
) ENGINE=aria;

CREATE TABLE `cache_url` (
  `url` char(22) NOT NULL,               -- URL hash
  `age` int NOT NULL,                    -- Cache timestamp
  `etag` char(22) NOT NULL               -- Associated etag
) ENGINE=aria;

CREATE TABLE `cache_data` (
  `etag` char(22) NOT NULL,              -- Content identifier
  `data` longblob NOT NULL               -- Cached content (may be compressed)
) ENGINE=aria;
```

## Key Features

### Request Tracking

The tracking system provides comprehensive visibility into PAD application usage:
- Session management with automatic tracking
- Individual request logging with full metadata
- Performance metrics (duration, response size)
- Storage of complete response data for debugging
- Links requests to sessions for user flow analysis

### Caching System

Three-table cache design for efficient storage and retrieval:
- **ETag-based caching**: Content identified by cryptographic hash
- **Age tracking**: Automatic cache expiration
- **URL mapping**: Fast lookup from URL to cached content
- **Separate data storage**: Optimized for large content

### URL Shortening (FastLinks)

The links table enables:
- Short, clean URLs for complex parameter sets
- Persistent link storage
- Variable serialization and restoration
- Bookmark-friendly URLs

### Performance Optimization

Design choices for performance:
- MyISAM engine for tracking (fast inserts, read-heavy workload)
- Aria engine for caching (better crash recovery, concurrent access)
- Appropriate index selection
- Fixed-width character fields for speed
- Efficient primary key design

## Integration with PAD Framework

The database integrates with PAD's core features:

1. **Configuration**: Database connection parameters defined in `/pad/config/config.php`:
   ```php
   $padSqlPadHost     = '127.0.0.1';
   $padSqlPadDatabase = 'pad';
   $padSqlPadUser     = 'pad';
   $padSqlPadPassword = 'pad';
   ```

2. **Tracking**: When tracking is enabled (`$padInfo` includes 'track'):
   - Sessions are created/updated in `track_session`
   - Each request is logged to `track_request`
   - Response data optionally stored in `track_data`

3. **Caching**: When server-side cache is enabled (`$padCacheServerType = 'db'`):
   - Cache lookups check `cache_url` for etag
   - Content retrieved from `cache_data`
   - Cache age validated against `cache_etag`

4. **FastLinks**: When short URLs are used:
   - Long parameter sets stored in `links`
   - Short codes included in URLs
   - Parameters restored from database on request

5. **Performance Monitoring**:
   - Track request duration for performance analysis
   - Monitor response sizes
   - Identify slow pages or sessions

## Installation

To set up the PAD database:

1. Run as MySQL root user or user with CREATE DATABASE privileges:
   ```bash
   mysql -u root -p < database.sql
   mysql -u root -p < pad.sql
   ```

2. Optionally add caching tables:
   ```bash
   mysql -u root -p < cache.sql
   ```

3. Verify installation:
   ```bash
   mysql -u pad -p pad
   SHOW TABLES;
   ```

## Configuration Options

Related configuration variables in `/pad/config/`:

### Tracking Configuration (`config/info/track.php`)
- `$padInfoTrack` - Enable/disable tracking
- `$padInfoTrackDbSession` - Track sessions to database
- `$padInfoTrackDbRequest` - Track requests to database
- `$padInfoTrackDbData` - Store response data in database

### Cache Configuration (`config/cache.php`)
- `$padCacheServerType` - Set to 'db' for database caching
- `$padCacheDbHost` - Cache database host
- `$padCacheDbDatabase` - Cache database name
- `$padCacheDbUser` - Cache database username
- `$padCacheDbPassword` - Cache database password

## Database Maintenance

### Clearing Old Tracking Data

```sql
-- Delete sessions older than 30 days
DELETE FROM track_session WHERE start < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Delete orphaned requests
DELETE track_request FROM track_request
LEFT JOIN track_session ON track_request.session = track_session.session
WHERE track_session.session IS NULL;

-- Delete orphaned data
DELETE track_data FROM track_data
LEFT JOIN track_request ON track_data.etag = track_request.etag
WHERE track_request.etag IS NULL;
```

### Clearing Expired Cache

```sql
-- Delete cache entries older than configured age
DELETE FROM cache_etag WHERE age < UNIX_TIMESTAMP() - 3600;
DELETE FROM cache_url WHERE age < UNIX_TIMESTAMP() - 3600;

-- Delete orphaned cache data
DELETE cache_data FROM cache_data
LEFT JOIN cache_etag ON cache_data.etag = cache_etag.etag
WHERE cache_etag.etag IS NULL;
```

### Optimizing Tables

```sql
OPTIMIZE TABLE track_session, track_request, track_data, links;
OPTIMIZE TABLE cache_etag, cache_url, cache_data;
```

## Architecture Notes

The database design reflects PAD's operational patterns:

- **Separation of concerns**: Tracking, caching, and links are separate subsystems
- **Denormalization**: Some redundancy (e.g., timestamps) for query performance
- **ETag-based identity**: Content-addressable storage using cryptographic hashes
- **Session linking**: Enables user flow analysis and session management
- **Flexible storage**: BLOB and TEXT fields for variable-length content
- **Engine selection**: Different storage engines optimized for specific use cases

This database layer provides essential infrastructure for PAD's advanced features while maintaining simplicity and performance.
