# StageOne - Modeling Portal

Welcome to StageOne, a premium modeling portfolio platform connecting talent with opportunities. This platform combines artistic portfolio showcase capabilities with social networking and business features for the modeling industry.

## Table of Contents
- [Features](#features)
- [Technologies](#technologies)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)

## Features

### Portfolio Management
- **Sessions**: Create themed photo sessions with titles, descriptions, locations, and dates
- **Image Galleries**: Upload and organize up to 50 photos per session with drag-and-drop ordering
- **Categories**: Organize content with multiple categories per session
- **Cover Images**: Set featured images for each session

### Social Features
- **Following System**: Follow other models, photographers, and industry professionals
- **Likes**: Public "heart" likes to show appreciation for work
- **Comments**: Moderate comments on sessions
- **Notifications**: Real-time notifications for interactions

### Business Tools
- **Casting Calls**: Create and apply for casting opportunities
- **Reference System**: Request and give professional references
- **Private Ratings**: Rate sessions privately (1-5 stars) for personal organization
- **Professional Networking**: Connect with photographers, stylists, and makeup artists

### User Profiles
- **Tabbed Interface**: Organize content with Sessions, Photos, About, Contact, and Stats tabs
- **Bio & Specialization**: Showcase professional details
- **Equipment & Links**: Share gear and social media profiles
- **Statistics**: View follower counts, session numbers, and engagement metrics

## Technologies

- **Backend**: PHP 8.4 with strict typing, prepared statements, and object-oriented design
- **Database**: MySQL 8 with proper indexing and UTF8MB4 character set
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 5.3
- **Security**: CSRF protection, password hashing, input validation, reCAPTCHA integration
- **File Handling**: Secure image upload with validation and proper storage organization

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/stageone.git
   cd stageone
   ```

2. Set up your web server to point to the project root

3. Create the database using the schema file:
   ```bash
   mysql -u username -p < schema.sql
   ```

4. Configure your database connection in `/includes/config.php`:
   ```php
   define('DB_HOST', 'your_host');
   define('DB_NAME', 'your_database');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

5. Set proper file permissions for upload directories:
   ```bash
   chmod 755 uploads/
   chmod 755 avatars/
   chmod 755 sessions/
   ```

## Database Schema

The application uses a comprehensive database schema defined in `schema.sql` with tables for:
- Users and profiles
- Sessions and session images
- Social features (likes, follows, comments)
- Business features (casting calls, applications)
- Messaging and notifications
- References and ratings

## API Endpoints

The application provides RESTful API endpoints in the `/api/` directory for:
- Authentication (login, register, logout)
- Social features (follow, like, comment)
- Content management (sessions, images)
- Messaging and notifications
- Business features (casting, references)