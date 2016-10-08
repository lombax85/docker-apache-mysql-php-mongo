#!/bin/bash
mongo admin --eval "db.createUser({ user: '$1', pwd: '$2', roles: [ { role: 'userAdminAnyDatabase', db: 'admin' } ] })"