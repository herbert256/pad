# PAD Framework Installation Utilities

## Overview
The install directory contains shell scripts for setting up and configuring the PAD framework environment. These scripts automate the installation of database schemas, configuration of the Apache web server, and initialization of required data directories.

## Purpose
This module provides automated installation and configuration tools for:
- Database setup and initialization
- Web server configuration
- Data directory creation
- System-level script deployment
- Complete framework installation orchestration

## Files

### Main Installation Script
- **install.sh** - Master installation script that orchestrates the complete PAD framework setup

### Component Scripts
- **db.sh** - Database initialization script that creates and populates SQL tables
- **data.sh** - Data directory setup script
- **apache.sh** - Apache web server configuration script
- **scripts.sh** - System scripts deployment

### Documentation
- **BUGS.md** - Known issues and bug tracking for installation procedures

## Key Features

### Master Installation (install.sh)
The main installation script performs the following operations:
1. **Root Check** - Verifies the script is run with root privileges
2. **Database Setup** - Executes db.sh to initialize database schemas
3. **Data Setup** - Runs data.sh to create necessary directories
4. **Apache Config** - Applies apache.sh to configure web server
5. **Scripts Deploy** - Executes scripts.sh for system script installation
6. **Service Restart** - Stops and starts Apache2 to apply changes

### Database Installation (db.sh)
Initializes the database by executing SQL schema files:
- **Framework Databases**:
  - database.sql - Core database structure
  - pad.sql - PAD framework tables
  - cache.sql - Caching system tables
- **Application Databases**:
  - database.sql - Application-specific schema
  - demo.sql - Demo data
  - classicmodels.sql - Example dataset

### Apache Configuration (apache.sh)
Configures Apache web server for the PAD framework:
- Updates DocumentRoot from `/var/www` to `/home/$usr/www`
- Modifies apache2.conf for custom document root
- Updates default site configuration (000-default.conf)
- Uses sed for in-place file modifications

## Installation Process

### Prerequisites
- Root access to the system
- MySQL/MariaDB server installed and running
- Apache2 web server installed
- Appropriate file permissions on PAD directories

### Running the Installation

```bash
# Execute as root
sudo /home/herbert/pad/install/install.sh
```

The script will:
1. Verify root privileges
2. Initialize all databases from SQL files
3. Set up data directories
4. Configure Apache web server
5. Deploy system scripts
6. Restart Apache to apply changes

### Database Setup Details

The database installation creates:
- **PAD system tables** - Framework internals, sessions, cache
- **Application tables** - Custom application data structures
- **Demo data** - Sample data for testing and development

SQL files are loaded directly into MySQL:
```bash
mysql < /home/herbert/pad/database/database.sql
mysql < /home/herbert/pad/database/pad.sql
# ... additional schema files
```

### Apache Configuration Details

The Apache configuration script updates paths to match the user's home directory:
- Replaces `/var/www` with `/home/$usr/www` in apache2.conf
- Updates `/var/www/html` to `/home/$usr/www` in site config
- Allows PAD to run from user directories instead of system paths

## Integration with PAD Framework

The installation module prepares the system environment for the PAD framework:

1. **Database Layer** - Creates all required tables and relationships for data storage
2. **Web Server** - Configures Apache to serve PAD applications correctly
3. **File System** - Establishes proper directory structure for data and caching
4. **System Services** - Ensures all dependencies are properly configured

## Usage Notes

### Security Considerations
- Scripts require root privileges for system-level changes
- Database credentials must be configured before running db.sh
- Apache configuration changes affect the entire web server

### Customization
- Edit SQL file paths in db.sh for custom database locations
- Modify apache.sh to adjust DocumentRoot paths
- Update install.sh to include additional setup steps

### Troubleshooting
- Check BUGS.md for known installation issues
- Verify MySQL service is running before database installation
- Ensure proper file permissions on script files (executable)
- Review Apache error logs if web server fails to start

### Re-installation
Scripts can be run multiple times:
- Database scripts will overwrite existing tables
- Apache configuration will replace previous settings
- Data directories will be recreated if missing

## File Locations

### PAD Framework Databases
- `/home/herbert/pad/database/database.sql`
- `/home/herbert/pad/database/pad.sql`
- `/home/herbert/pad/database/cache.sql`

### Application Databases
- `/home/herbert/app/_database/database.sql`
- `/home/herbert/app/_database/demo.sql`
- `/home/herbert/app/_database/classicmodels.sql`

### Apache Configuration
- `/etc/apache2/apache2.conf`
- `/etc/apache2/sites-enabled/000-default.conf`

## Post-Installation

After successful installation:
1. Apache2 service is restarted automatically
2. Database tables are ready for use
3. Web server points to correct document root
4. PAD framework is ready to process requests

Verify installation by:
- Checking Apache status: `service apache2 status`
- Testing database connection: `mysql -e "SHOW DATABASES;"`
- Accessing PAD application through web browser
- Reviewing Apache error logs for any issues
