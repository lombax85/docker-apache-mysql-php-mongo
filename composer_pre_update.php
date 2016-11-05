<?php

/** TODO
 *	Permit to remove the "extra" section in the composer.json. Add a script that copies 
 *  from vendor/lombax85/docker to the root directory
 */

// CONFIG
$env_source_filename = "./docker/.env";
$env_backup_file = "./.env.docker.backup";

$read = file_get_contents($env_source_filename);
// write the file
file_put_contents($env_backup_file, $read);

echo "Created a new backup copy of the .env file in $env_backup_file \n";
