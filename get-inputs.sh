#!/bin/bash
letters=`find entries -type d`
for letter in $letters; do
    if [[ "$letter" != "entries" ]]; then
        # files=`find ${letter} -type f -name "*.tex" -print0`
        if [ -e "${letter}.tex" ]; then
            unlink "${letter}.tex"
        fi
        while IFS= read -r -d $'\0' file; do
            echo "\\input \"$file\"" >> "${letter}.tex"
        done < <(find ${letter} -type f -name "*.tex" -print0)
        # http://stackoverflow.com/questions/1116992/capturing-output-of-find-print0-into-a-bash-array
        # Note that teh redirection construct used here (cmd < <(cmd2)) is
        # similar to, but not quote the same as the more usual pipeline
        # (cmd2 | cmd 1) -- if the commands are shell builtins (e.g., while),
        # the pipeline version executes them in subshells, and any variables
        # they set (e.g., the array a) are lost when they exit. cmd1 < <(cmd2)
        # only runs cmd2 in a subshell, so the array lives past its
        # construction.
    fi
done

