#!/bin/bash

docker run --rm -v $(pwd):/app -p 8181:8181 -p 8080:8080 $(docker build -q .)