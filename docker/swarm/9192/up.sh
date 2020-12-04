#!/bin/bash
docker-compose -p dbc_mall up -d
docker image prune -f