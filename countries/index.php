<?php
$basePath = './'; // Ganti dengan path folder yang sesuai

function getFileSize($filePath) {
    return file_exists($filePath) ? filesize($filePath) : 0;
}

function formatSizeUnits($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $bytes /= 1024, $i++);
    return round($bytes, 2) . ' ' . $units[$i];
}

// Fungsi rekursif untuk mendapatkan semua file dan subfolder
function getFilesAndFolders($folder) {
    $result = [];

    $contents = scandir($folder);
    $contents = array_diff($contents, array('.', '..', 'index.php'));

    foreach ($contents as $item) {
        $path = $folder . '/' . $item;

        if (is_dir($path)) {
            $result[$item] = getFilesAndFolders($path);
        } else {
            $result[] = $item;
        }
    }

    return $result;
}

// Fungsi untuk menghasilkan path relatif dari root
function getRelativePath($path) {
    return str_replace('./', '', $path);
}

// Cek apakah folder ada
if (is_dir($basePath)) {
    // Dapatkan semua file dan subfolder
    $structure = getFilesAndFolders($basePath);

    // Tampilkan daftar file dan subfolder dengan heading
    function displayStructure($structure, $currentPath = '') {
        echo '<ul>';
        foreach ($structure as $key => $item) {
            echo '<li>';

            $itemPath = $currentPath . '/' . $key;

            if (is_array($item)) {
                echo "<a href='." . getRelativePath($itemPath) . "'>$key</a>";
                displayStructure($item, $itemPath);
            } else {
                $fileSize = getFileSize('./' . getRelativePath($currentPath) . '/' . $item);
                $fileSize = formatSizeUnits($fileSize);

                echo "<a href='." . getRelativePath($currentPath) . '/' . $item . "'>$item " . "</a>" . "<small>&nbsp;" . $fileSize . "</small>";

            }

            echo '</li>';
        }
        echo '</ul>';
    }

    echo '<h2>Daftar File dan Folder:</h2>';
    displayStructure($structure);
} else {
    echo "Folder tidak ditemukan.";
}
?>
