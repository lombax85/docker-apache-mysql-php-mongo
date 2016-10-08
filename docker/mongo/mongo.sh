#!/bin/bash
mongo admin --eval "db.createUser({ user: '$0', pwd: '$1', roles: [ { role: 'userAdminAnyDatabase', db: 'admin' } ] })"