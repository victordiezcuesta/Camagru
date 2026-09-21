{
    echo "==================== TREE ===================="
    tree -I '.git|libs'

    echo
    echo "==================== ARCHIVOS ===================="

    find . -type f \
        ! -path './libs/*' \
        ! -path './.git/*' \
        ! -name 'notascamagru.txt' \
        ! -name 'actualizar.sh' |
    sort |
    while read -r file; do
        echo
        echo "=================================================="
        echo "===== $file ====="
        echo "=================================================="
        cat "$file"
        echo
    done
} > notascamagru.txt