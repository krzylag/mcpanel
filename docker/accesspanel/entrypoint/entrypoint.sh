#!/bin/sh

if /usr/bin/find "/entrypoint.d/" -mindepth 1 -maxdepth 1 -type f -print -quit 2>/dev/null | read v; then
    echo "$0: /entrypoint.d/ is not empty, will attempt to perform configuration"

    echo "$0: Looking for shell scripts in /entrypoint.d/"
    find "/entrypoint.d/" -follow -type f -print | sort -V | while read -r f; do
        case "$f" in
            *.sh)
                if [ -x "$f" ]; then
                    echo "$0: Launching $f";
                    "$f"
                else
                    echo "$0: Ignoring $f, not executable";
                fi
                ;;
            *) echo "$0: Ignoring $f";;
        esac
    done

    echo "$0: Configuration complete; ready for start up"
else
    echo "$0: No files found in /entrypoint.d/, skipping configuration"
fi

exec "$@"
