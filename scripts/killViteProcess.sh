#! bash

pid=$(ps aux | grep '[v]ite' | awk '{print $2}')

if [ -n "$pid" ]; then
    kill $pid
    echo "Vite server stopped."
else
    echo "No Vite server running."
fi
