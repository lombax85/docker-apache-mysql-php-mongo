<?php

/** TODO
 *	Permit to remove the "extra" section in the composer.json. Add a script that copies 
 *  from vendor/lombax85/docker to the root directory
 */

// CONFIG
$env_source_filename = "./docker/.env.composer";
$env_dest_filename = "./docker/.env";

/**
 * Create a default .env file with an unique project name
 */
if (!file_exists($env_dest_filename))
{
	// read the .env.composer file
	$read = file_get_contents($env_source_filename);

	// timestamp
	$date = new DateTime();
	$ts = $date->getTimestamp();

	//replace the project name with a unique one
	$read = str_replace("COMPOSE_PROJECT_NAME=docker", "COMPOSE_PROJECT_NAME=docker_".$ts, $read);

	// write the file
	file_put_contents($env_dest_filename, $read);

	echo "Created a default .env file \n";
} else {
	echo ".env file already present, not touched \n";
}