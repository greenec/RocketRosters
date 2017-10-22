# Rocket Rosters
I built Rocket Rosters in order to make the management of rosters for sports teams in tournaments simpler.

## Features
* Tournament creation and management
* Team creation and management
* Player info management
* Recruiters attending tournaments may view information of players in the tournament
* AJAX calls for a seamless user experience
* Caching for faster load times, though it may be disabled in favor of fresher data

This site is live at https://rocketrosters.com.

This project has been open sourced, and may be used as is. As of now, I don't have any plans to maintain this any further.

## Installation
* Copy all files (excluding the SQL dump) to your web server
* Create a database from roster_db.sql and grant a mySQL user the necessary privileges
* Alter include/db.php to match your newly configured database
* Alter include/classes/Mail.php to match your SMTP mail server
* Test to make sure everything is working
