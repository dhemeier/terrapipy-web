#!/bin/bash

# Start runit and services
# it is not possible to pass environment variables to runit
# so we just save them in a file which we can source from
# run scripts.
export > /etc/envvars
exec runsvdir -P /etc/service
