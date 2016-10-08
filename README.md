How to use this project
-----------------------

- Download as zip (if you clone it, remove the .git directory)
- Put your project files inside the root directory or inside a "project" subdirectory (the name of the subdirectory can be set later). 
- copy .env.example to .env
- Open .env file and set your environment variables

- on mac: enable file sharing on docker/data and docker/logs
- then do:

```
docker-compose build apache2 mysql workspace mongo php-fpm
docker-compose up apache2 mysql mongo
```

###ATTENZIONE:

la cartella /docker/data conterrà i dati di database, sessioni ecc. Sottoporre il contenuto a backup (per mysql è consigliato mysqldump e non il backup diretto della cartella)


HOSTNAME:
Docker crea una rete privata virtuale, isolata, per tutti i container che fanno parte dello stesso progetto (si basa sul nome della directory root)
Per raggiungere un container da un altro, usare l'hostname
Quindi, se la tua web app deve raggiungere il database mysql, nel file di configurazione dovrai inserire l'hostname "mysql"
Docker risolverà automaticamente l'hostname e lo tradurrà nell'ip del container di destinazione.
E' possibile ottenere anche l'ip, ma non usarlo: può cambiare

```
root@7aa4b96361fb:/var/www# ping mysql
PING mysql (172.19.0.4) 56(84) bytes of data.
64 bytes from mongo_mysql_1.mongo_default (172.19.0.4): icmp_seq=1 ttl=64 time=0.148 ms
```

In questo progetto esistono i seguenti container/hostname

workspace
mysql
php-fpm
apache2
mongo



###TODO
- aggiungere la possibilità di includere questo progetto come dipendenza composer