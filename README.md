Silex Jobboard
==============

a job board application written with Silex
------------------------------------------

author : M.PARAISO <mparaiso@online.fr>

code license : GPL
graphic assets license : all rights reserved &copy; M.PARAISO

### REQUIREMENTS

- PHP 5.3
- a pdo compatible database ( like Mysql )
- a webserver

### INSTALLATION

Define the following server variables :

+ JOBB_DRIVER : pdo driver name ( ex: pdo_mysql , pdo_sqlite )
+ JOBB_DBNAME : database name
+ JOBB_USER : database user
+ JOBB_PASSWORD : database password
+ JOBB_HOST : database host (optional)
+ JOBB_PORT : database port (optional)

### Routes

<pre>
[router] Current routes
Name                    Method   Scheme Host Path
home                    ANY      ANY    ANY  /
job_principe            ANY      ANY    ANY  /job/
job_post                GET|POST ANY    ANY  /job/post-a-job
job_edit                GET|POST ANY    ANY  /job/edit-a-job/{token}
job_remove              ANY      ANY    ANY  /job/remove-job/{token}
job_admin               ANY      ANY    ANY  /job/job-admin/{token}
job_detail              GET      ANY    ANY  /job/{company}/{location}/{id}/{position}
category_read           ANY      ANY    ANY  /category/category/{id}/{name}
mp_crud_job_index       ANY      ANY    ANY  /admin/job/
mp_crud_job_create      ANY      ANY    ANY  /admin/job/create
mp_crud_job_update      ANY      ANY    ANY  /admin/job/update/{id}
mp_crud_job_delete      ANY      ANY    ANY  /admin/job/delete/{id}
mp_crud_job_read        ANY      ANY    ANY  /admin/job/{id}
mp_crud_category_index  ANY      ANY    ANY  /admin/category/
mp_crud_category_create ANY      ANY    ANY  /admin/category/create
mp_crud_category_update ANY      ANY    ANY  /admin/category/update/{id}
mp_crud_category_delete ANY      ANY    ANY  /admin/category/delete/{id}
mp_crud_category_read   ANY      ANY    ANY  /admin/category/{id}
</pre>

### TODO

+ <strike>Day 1: Starting up the Project</strike> DONE
+ <strike>Day 2: The Project</strike> DONE
+ <strike>Day 3: The Data Model</strike> DONE
+ Day 4: The Controller and the View
+ Day 5: The Routing
+ Day 6: More with the Model
+ Day 7: Playing with the Category Page
+ Day 8: The Unit Tests
+ Day 9: The Functional Tests
+ Day 10: The Forms
+ Day 11: Testing your Forms
+ Day 12: The Admin Generator
+ Day 13: The User
+ Day 14: Feeds
+ Day 15: Web Services
+ Day 16: The Mailer
+ Day 17: Search
+ Day 18: AJAX
+ Day 19: Internationalization and Localization
+ Day 20: The Plugins
+ Day 21: The Cache
+ Day 22: The Deployment
+ Day 23: Another Look at symfony
