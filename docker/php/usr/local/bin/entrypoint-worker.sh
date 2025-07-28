#!/bin/bash

exec php bin/console messenger:consume rabbitmq
