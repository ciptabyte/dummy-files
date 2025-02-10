<?php
/**
 * This script zips the current folder and allows the user to download it.
 */

// Set the name for the zip file
$zipFileName = basename(__DIR__) . '.zip';

// Function to recursively add files to the zip archive
function addFolderToZip($folderPath, $zip, $rootLength)
{
    $folderPath = rtrim($folderPath, '/\\') . DIRECTORY_SEPARATOR;
    $files = scandir($folderPath);

    foreach ($files as $file) {
        if (in_array($file, ['.', '..', 'zip.php', 'index.php'])) {
            continue;
        }

        $filePath = $folderPath . $file;
        $relativePath = substr($filePath, $rootLength);

        if (is_dir($filePath)) {
            $zip->addEmptyDir($relativePath);
            addFolderToZip($filePath, $zip, $rootLength);
        } else {
            $zip->addFile($filePath, $relativePath);
        }
    }
}

// Create a new zip archive
$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    addFolderToZip(__DIR__, $zip, strlen(__DIR__) + 1);
    $zip->close();

    // Set headers to initiate the download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
    header('Content-Length: ' . filesize($zipFileName));

    // Output the zip file
    readfile($zipFileName);

    // Delete the zip file after download
    unlink($zipFileName);
    exit;
} else {
    echo 'Failed to create zip file.';
}
