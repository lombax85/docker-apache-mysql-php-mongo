## <a name="contents-link"></a>Table Of Contents

* [Project Description](#description-link)
* [Requirements](#requirements-link)
* [Quick Install (via Composer)](#quickinstall-link)
  * [Important informations and warnings](#importantinformations-link)
  * [Data Directory](#datadirectory-link)
* [Start](#start-link)
* [Interacting with projects](#interact-link)
* [Hostnames](#hostnames-link)
* [Alternative install methods](#alternative-link)
* [Troubleshooting](#troubleshooting-link)
* [Actual Issues](#actualissues-link)
* [Fixed Issues](#fixedissues-link)
* [Todo](#todo-link)

## <a name="description-link"></a>PROJECT DESCRIPTION

Get your **PHP/MySQL** project up and running within minutes with the power of Docker and Composer!
With few commands, you'll have your development, staging and production infrastructures ready-to-go taking advantage of **Docker** containers.
Within this project, you'll find a container for the following dependencies:
- apache
- php-fpm
- mysql
- mongodb
- couchdb

Moreover, database data and sessions are managed with a specific container, and a last container is provided as the workspace (a special container you can use to run CLI commands).  
Thanks to Composer, this project can be easily integrated and encapsulated into you existing webapp, permitting you to deploy it faster on development machines, staging servers and production servers.

## <a name="requirements-link"></a>REQUIREMENTS

- docker-compose, min version 1.8
- docker, min version 1.10

If you want to install via composer:
- composer
- php, min version 5.6


## <a name="quickinstall-link"></a>QUICK INSTALL via Composer


NOTE: for alternative install methods, look at the dedicated section 

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

EXTRA: add /docker and /docker_data in your project's .gitignore file

#### <a name="importantinformations-link">IMPORTANT INFORMATIONS AND WARNING</a>
- The whole project will be installed inside the "docker" subdir, and a "docker_data" directory will appear when you start you containers.
- inside the "docker" directory, you will find a .env file where you can set your additional environment variables (the file is pre-configured and no need of additional configuration is needed to get it up and running)

#### <a name="datadirectory-link">DATA DIRECTORY</a>

The `./docker_data` directory containes all data of **databases and sessions**.
If you use this setup in a production environment, **don't forget to backup all data** with the appropriate tools (example: mysqldump for mysql).   
The `./docker_data` directory is shared among containers using directory binding and is kept between container rebuilds.   
For this reason, **when you rebuild - for example - your mysql container, the data are not lost**. 
However, pay attention because if you change your mysql engine to somethings not compatible with the content of your data directory, the content itself can become corrupted.

By default, the data directory is configured to be inside `./docker_data` (if you install via composer) or `./docker/data` if you use the `.env.example` file.

The directory is created when you start your containers the first time. If you want to change this path, please don't place the directory inside the `./docker` folder, since the folder is recreated every time you run "composer update" and you'll lost data.

 


## <a name="start-link"></a>START


- execute this commands (if you don't need a specific engine, omit it in the "up" command)

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

## <a name="interact-link"></a>INTERACTING with projects

The "workspace" container should be used for all cli commands (composer install/update, artisan)

```
docker-compose exec workspace bash
```

will give you a shell inside the www directory.
If you prefer, you can send your command directly without using the shell. For example, to send a "php artisan migrate", simply do

```
docker-compose exec workspace php artisan migrate
```
 

## <a name="hostnames-link"></a> HOSTNAMES:

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

## <a name="alternative-link"></a> ALTERNATIVE INSTALL METHODS

#### Alternative - Existing project - install in root directory
- Download this repository as zip
- copy "docker-compose.yml" file and "docker" directory in your project's root directory
- copy the ".env.example" file into your project directory and rename it into ".env"
	NOTE: if your project has already a file called .env, you can use a subdirectory as explained in "Existing project - subdirectory"

#### Alternative - Existing project - install in subdirectory
- Download this repository as zip
- copy your project files inside a "project" subfolder


#### Alternative - New project - install in subdirectory
- Download as zip (if you clone it, remove the .git directory)
- Put your project files inside the root directory or inside a "project" subdirectory (the name of the subdirectory can be set later) OR, alternative, copy 


## <a name="troubleshooting-link"></a>ADDITIONAL SETUP and Troubleshooting
- on mac: enable file sharing on ./docker_data and ./docker folders


## <a name="actualissues-link"></a>ACTUAL ISSUES


- If you stop (ctrl+c) during "docker-compose up" during the first container startup, the content of /docker/data can became corrupt or not correctly initialized. In this case, for example, you won't be able to connect to MySQL.
To solve:

```
docker-compose stop
rm -Rf ./docker/data/mysql/*
```
NOTE: if you wipe MongoDB Data, don't forget to re-add the default user

## <a name="fixedissues-link"></a>FIXED ISSUES

- including as a composer dependencies is, by now, only for testing and development machines. There is a known issue where the ./docker/data directory (the directory containing database data) is deleted if the package is updated via "composer update". This will be solved in a future release, if you plan to use this project in a production environment don't use composer, use other inclusion methods explained in the INSTALL section. FIXED BY: now the data directory is created inside your main project's directory
- If you install inside two different projects on the same machine, you have to rename the container directory ("docker") to something unique. FIXED BY: the install script now creates a .env with an unique project name, docker_TIMESTAMP

## <a name="todo-link"></a> TODO

- create install.php install script to replace the post-install and post-update hooks
