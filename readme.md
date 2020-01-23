#Iterato Assessment
It is the application of Assesment. Where I have used a framework named CodeIgniter 3.
It is MVC framework. 

I maintained psr2 in my working directory. Also developed a simple ORM, which is located in application/core/AppModel.php
## Directory Information
* My Working directory is application
* assets contains public file like images, css, js
* Framework directory ( CodeIgniter) 
* verdor is composer vendor file
* As it is MVC framework so, contoller is located in application/controllers, model located in application/models and view located in application/views

## Configuration
* User URL configuration is in application/config/appconfig.php
* Database configuration is in application/config/database.php
* if rewrite doesn't work then please uncomment line number 3 in htaccess and set RewriteBase

## Database dump sql located in database directory
## API Information
* API endpoint is installed domain/api
* Task api is http(s)://domain/api/task , it accepted post and put as required
* for put method endpoint is http(s)://domain/api/task/<task_id>, this <task_id> is task id
 
## Option description
* Dashboard controller located in application/controllers/Dashboard.php
* API controller located in  application/controllers/api/task.php

#### Thank you

