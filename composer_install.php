<?php

/** TODO
 *	Permit to remove the "extra" section in the composer.json. Add a script that copies 
 *  from vendor/lombax85/docker to the root directory
 */

// CONFIG
$env_source_filename = "./docker/.env.composer";
$env_dest_filename = "./docker/.env";
$env_backup_file = "./.env.docker.backup";

/**
 * Create a default .env file with an unique project name
 */
if (!file_exists($env_dest_filename))
{
    if (file_exists($env_backup_file))
    {
        // a backup exists, use that file
        $read = file_get_contents($env_backup_file);
        // write the file
        file_put_contents($env_dest_filename, $read);
        echo "Created .env file in $env_dest_filename using your backup copy at $env_backup_file \n";
    } else {
        // no backup exists, copy the example file
        $read = file_get_contents($env_source_filename);
        
        // replace the project name
        // timestamp
	$date = new DateTime();
	$ts = $date->getTimestamp();

	//replace the project name with a unique one
	$read = str_replace("COMPOSE_PROJECT_NAME=docker", "COMPOSE_PROJECT_NAME=docker_".$ts, $read);
        
        // since the user doesn't have a backup file, create one
        file_put_contents($env_backup_file, $read);
        
        // write the file
        file_put_contents($env_dest_filename, $read);
        echo "Created a new $env_dest_filename file and added a backup copy to $env_backup_file \n";
    }

} else {
	echo ".env file already present, not touched \n";
}