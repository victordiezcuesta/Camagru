{
    echo "==================== TREE ===================="
    tree -I '.git|libs'

    echo "==================== ARCHIVOS ===================="

    find . -type f \
        ! -path './libs/*' \
        ! -path './.git/*' \
        ! -name 'notascamagru.txt' \
        ! -name '.gitignore' \
        ! -name 'actualizar.sh' |
    sort |
    while read -r file; do
        echo
        echo "===== $file ====="
        cat "$file"
        echo
    done
} > notascamagru.txt