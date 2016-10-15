DESCRIPTION
-----------------------

This project demonstrates how to use docker-compose to split a sample php application into docker containers.  
Each container runs a component: apache2, php-fpm, mysql, mongodb, couchdb and finally one container for data (binding project directory and data directories to the container itself), and one container for the workspace (a special container you can use to run CLI commands).  
This project can be easily integrated into you existing webapp to permit you to deploy it faster on development machines, staging servers and production servers.

REQUIREMENTS
-----------------------
- docker-compose, min version 1.8
- docker, min version 1.10

If you want to install via composer:
- composer
- php, min version 5.6


INSTALL
-----------------------

Depending on your project's structure, you can use different approaches.


### Existing project - composer dependency (EXPERIMENTAL - test well before using in production and backup your data)
- In the "extra" section of your project's composer.json file, add
```
	"extra": {
        "installer-paths": {
            "docker/": ["lombax85/docker-apache-mysql-php-mongo"]
        }
    }
```


- In the "scripts" section, "post-install-cmd" and "post-update-cmd" sub-section of your project's composer.json file, add
```
	"scripts": {
    	"post-install-cmd": [
        	"php docker/composer_install.php"
    	],
    	"post-update-cmd": [
            "php docker/composer_install.php"
        ]
    }
```
- Add the dependency using the command
```
composer require lombax85/docker-apache-mysql-php-mongo
```
- The whole project will be installed inside the "docker" subdir, you will find a .env file where you can set your additional environment variables

EXTRA: add /docker and /docker_data in your project's .gitignore file

##WARNING
Informations about the data directory: in the .env.composer file, the data directory is configured to be inside "docker_data", placed in your main project's dir. The directory is created when you start your containers the first time. If you want to change this path, please don't place the directory inside the "docker" folder, since the folder is recreated every time you run "composer update" and you'll lost data.


### Alternative - Existing project - install in root directory
- Download this repository as zip
- copy "docker-compose.yml" file and "docker" directory in your project's root directory
- copy the ".env.example" file into your project directory and rename it into ".env"
	NOTE: if your project has already a file called .env, you can use a subdirectory as explained in "Existing project - subdirectory"

### Alternative - Existing project - install in subdirectory
- Download this repository as zip
- copy your project files inside a "project" subfolder


### Alternative - New project - install in subdirectory
- Download as zip (if you clone it, remove the .git directory)
- Put your project files inside the root directory or inside a "project" subdirectory (the name of the subdirectory can be set later) OR, alternative, copy 
- copy .env.example to .env


ADDITIONAL SETUP
-----------------------
- go to the directory containing the "docker-compose.yml" file. If you installed via composer, it's inside your "docker" subdirectory
- open the .env file and set your environment variables
- on mac: enable file sharing on ./docker/data and ./docker/logs folders


START
-----------------------

- execute this command

```
docker-compose build apache2 mysql workspace mongo php-fpm couchdb
docker-compose up -d apache2 mysql mongo couchdb
```

- After these commands you'll have your containers up and running, use `docker ps` to see them
- Now do some post-install things:
	- MongoDB: Unlike MySQL, MongoDB doesn't allow to set default username and password prior to installation. For this reason, you must set them with a post-run script. To set default user and password for mongodb, type
	```
	docker-compose exec mongo sh /mongo.sh user password
	```

Interacting with project
-----------------------
The "workspace" container should be used for all cli commands (composer install/update, artisan)

```
docker-compose exec workspace bash
```

will give you a shell inside the www directory.
If you prefer, you can send your command directly without using the shell. For example, to send a "php artisan migrate", simply do

```
docker-compose exec workspace php artisan migrate
```


Warning, about ./docker/data directory:
-----------------------

The ./docker/data folder containes all data of databases and sessions.
If you use this setup in a production environment, don't forget to backup all data with the appropriate tools (example: mysqldump for mysql).   
The ./docker/data directory is shared among containers using directory binding and is kept between container rebuilds.   
For this reason, when you rebuild - for example - your mysql container, the data are not lost. However, pay attention because if you change your mysql engine to somethings not compatible with the content of your data directory, the content itself can become corrupted.  


HOSTNAMES:
-----------------------

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


EXTRA - QUICK DEMO
-----------------------
At this url: http://www.lombax.it/files/project.tar
You can find a ready-to-use laravel project that uses mysql and mongodb.  
The .env file of the Laravel application is already configured and all composer dependencies already loaded, so to finish the setup you have to do only few things:

- clone this repo
- do:
	```
	cp .env.example .env
	```
	NOTE: The .env.example file included in this project is already configured to support this installation, so simply copy .env.example to .env

- do: 
```
docker-compose build apache2 mysql workspace mongo php-fpm
docker-compose up -d apache2 mysql mongo
```
- copy the .tar content into the "project" subdirectory (remember to copy hidden files to)

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

ACTUAL ISSUES
-----------------------

- If you stop (ctrl+c) during "docker-compose up" during the first container startup, the content of /docker/data can became corrupt or not correctly initialized. In this case, for example, you won't be able to connect to MySQL.
To solve:

```
docker-compose stop
rm -Rf ./docker/data/mysql/*
```
NOTE: if you wipe MongoDB Data, don't forget to re-add the default user

FIXED ISSUES
-----------------------
- including as a composer dependencies is, by now, only for testing and development machines. There is a known issue where the ./docker/data directory (the directory containing database data) is deleted if the package is updated via "composer update". This will be solved in a future release, if you plan to use this project in a production environment don't use composer, use other inclusion methods explained in the INSTALL section. FIXED BY: now the data directory is created inside your main project's directory
- If you install inside two different projects on the same machine, you have to rename the container directory ("docker") to something unique. FIXED BY: the install script now creates a .env with an unique project name, docker_TIMESTAMP

TODO
-----------------------
- create install.php install script to replace the post-install and post-update hooks
