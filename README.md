What
-----------------------

This project demonstrates how to use docker-compose to split a sample php application into docker containers.  
Each container runs a component: apache2, php-fpm, mysql, mongodb, and finally one container for data (binding project directory and data directories to the container itself), and one container for the workspace (a special container you can use to run CLI commands).  
This project can be easily integrated into you existing webapp to permit you to deploy it faster on development machines, staging servers and production servers.

How to use this project
-----------------------

### Existing project - root directory
- Download this repository as zip
- copy "docker-compose.yml" file and "docker" directory in your project's root directory
- copy the ".env.example" file into your project directory and rename it into ".env"
	NOTE: if your project has already a file called .env, you can use a subdirectory as explained in "Existing project - subdirectory"

### Existing project - subdirectory
- Download this repository as zip
- copy your project files inside a "project" subfolder

### New project
- Download as zip (if you clone it, remove the .git directory)
- Put your project files inside the root directory or inside a "project" subdirectory (the name of the subdirectory can be set later) OR, alternative, copy 
- copy .env.example to .env

## Then:
- open the .env file and set your environment variables
- on mac: enable file sharing on docker/data and docker/logs
- open the shell in the root directory of this project and type:

```
docker-compose build apache2 mysql workspace mongo php-fpm
docker-compose up apache2 mysql mongo
```

- After there commands, you'll have your containers up and running, use `docker ps` to see them
- Now do some post-install things:
	- MongoDB: Unlike MySQL, MongoDB doesn't allow to set default username and password prior to installation. For this reason, you must set them with a post-run script. To set default user and password for mongodb, type
	```
	docker-compose exec mongo sh /mongo.sh user password
	```

###Interacting with project:
The "workspace" container should be used for all cli commands (composer install/update, artisan)

```
docker-compose exec workspace bash
```

will give you a shell inside the www directory.


###Warning, about /docker/data directory:

the folder /docker/data container all data from databases, sessions and other important data.
If you use this setup in a production environment, don't forget to backup all data with the appropriate tools (example: mysqldump for mysql).   
The /docker/data directory is shared among containers using directory binding and is kept between container rebuilds.   
For this reason, when you rebuild - for example - your mysql container, the data are not lost. However, pay attention because if you change your mysql engine to somethings not compatible with the content of your data directory, the content itself can become corrupted.  

###HOSTNAME:

Docker creates a virtual private and isolated network for all containers of the same project (it uses the root directory name as a prefix).  
To reach one container from another (for example for reaching mysql container from php-fpm) simply use the hostname.
The hostname is the name of the container in the docker-compose.tml file.  
Don't use the private ip because it can change at any time.  

So, when you have to configure your mysql server hostname in your web app's config file, simply type "mysql"

> MYSQL_HOST=mysql

If you bash into a container you'll see 

```
root@7aa4b96361fb:/var/www# ping mysql
PING mysql (172.19.0.4) 56(84) bytes of data.
64 bytes from mongo_mysql_1.mongo_default (172.19.0.4): icmp_seq=1 ttl=64 time=0.148 ms
```

In this project, these containers/hostname exists

workspace
mysql
php-fpm
apache2
mongo

### EXTRA - DEMO
At this url: http://www.lombax.it/files/project.tar
You can find a ready-to-use laravel project that uses mysql and mongodb.  
The .env file of the Laravel application is already configured and all composer dependencies already loaded, so to finish the setup you have to do only few things:

- clone this repo
- do: 
```
docker-compose build apache2 mysql workspace mongo php-fpm
docker-compose up apache2 mysql mongo
```
- copy the .tar content into the "project" subdirectory
- The .env.example file included in this project is already configured to support this installation, so simply copy .env.example to .env
- add this user to MongoDB:
	```
	docker-compose exec mongo sh /mongo.sh localuser secret
	```
- run laravel migration
```
docker-compose exec workspace php artisan migrate
```

Go to these urls to see the results:
	- http://localhost/mysql
	- http://localhost/mongo

The test code is in ./project/routes/web.php


###TODO
- add the possibility to declare this project as a composer.json dependency