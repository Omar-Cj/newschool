# Onest Drax Laravel Admin Dashboard and UI Kit

Onest Drax Laravel Admin Dashboard and UI Kit. Developing an admin dashboard template from scratch is not only time consuming but also an expensive process. Fortunately, you can develop a project very quickly with a prebuilt dashboard template. But it is a bit difficult to find the right template from thousands of options. In this article, we have listed the best admin panel templates including bootstrap, Laravel, VueJs, and Angular admin panel templates. Also, you will get some awesome free dashboard templates here. Without further ado, let’s start.

## Features

-   Responsive Design

## Template Features

-   RTL Version Includes
-   Laravel HTML5 MIX
-   New Laravel
-   PHP Version 7.4.12
-   Light Version & Dark Versions
-   Left side menu and Top menu dashboard design
-   Left side menu dashboard design
-   Top menu dashboard design
-   PHP Framework Laravel
-   MVC architecture Support
-   Package management with NPM
-   40 + Plugins
-   5 Dashboard variations
-   2 Type of menus designed dashboard template
-   6 types of Charts
-   Responsive Bootstrap 4 Framework
-   Well Commented Code
-   Data Tables
-   File upload
-   Easy to customize
-   Neat, clean and simple design
-   TimeLine
-   Editable Text
-   Message Chat
-   Google Fonts
-   More Maps
-   Mail Inbox
-   10 Types of Icon sets
-   Lock screen Pages
-   Error Pages
-   Shopping Cart
-   Regular Updates
-   Form Elements
-   Under Construction Page
-   Invoice Page
-   Calendar
-   Media Object
-   Pricing Tables
-   Very Easy to Create your Own Site
-   More Widgets
-   Image Gallery
-   W3C Validated
-   24 \* 7 Professional Company Support

## saas migrate command
 - php artisan module:migrate-fresh --seed MainApp
 - php artisan module:migrate-fresh MainApp
 - php artisan module:seed MainApp


 ## for single school running migration command
 - php artisan migrate:fresh --seed --path=database/migrations/tenant
 - APP_SAAS=false in env
 - CACHE_DRIVER=array in env
 - Notification job by default send with out queue 
 - but when we set NOTIFICATION_JOB=queue notification will be send by queue


 ## for saas school running migration command
 - php artisan migrate:fresh --path=modules/MainApp/database/migrations
 - php artisan module:seed MainApp
 - APP_SAAS=true in env
 - APP_MAIN_APP_URL=school-management.test


 php8 artisan module:migrate-fresh --seed MainApp
 php8 artisan module:migrate-fresh MainApp
 php8 artisan module:seed MainApp


# VehicleTracker Module Activated
- open `modules_statuses.json` and change VehicleTracker `false` to `true`
- run `php artisan module:migrate VehicleTracker` 
- run `php artisan module:seed VehicleTracker` 