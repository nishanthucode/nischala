<?php
require 'backend/config.php';
\ = pdo();
\ = \->query('SELECT id, image_path FROM gallery')->fetchAll(PDO::FETCH_ASSOC);

\ = 'uploads/gallery';
if (!is_dir(\)) {
    mkdir(\, 0777, true);
}

foreach (\ as \) {
    \ = \['image_path'];
    if (\ && !file_exists(\)) {
        // Download a random image
        \ = 'https://picsum.photos/800/600?random=' . \['id'];
        \ = @file_get_contents(\);
        if (\) {
            file_put_contents(\, \);
            echo \"Created placeholder for {\}\n\";
        } else {
            echo \"Failed to download for {\}\n\";
        }
    }
}
echo \"Done fixing local images!\n\";
