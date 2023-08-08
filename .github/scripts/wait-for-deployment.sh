#!/bin/bash

version=$(cat $1)

for i in {1..30}; do # Try for 5 minutes
    sleep 10

    deployed_version_json=$(curl -s https://staging.centify.de/deployed-version)
    deployed_version=$(echo "$deployed_version_json" | jq -r '.version')

    echo $deployed_version
    echo $deployed_version_json
    echo expected_version
    echo $version

    if [[ "$deployed_version" == "$version" ]]; then
        echo "Deployment complete"
        exit 0
    fi
done

echo "Deployment did not complete in time"
exit 1
