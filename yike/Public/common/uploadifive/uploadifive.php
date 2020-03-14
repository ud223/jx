<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory
$uploadDir = '/Public/uploads/';

// Set the allowed file extensions
$fileTypes = array('mp3', 'info', 'update', 'ipa', 'apk', 'xls', 'xlsx','jpg','png','gif','jpeg'); // Allowed file extensions

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
    $tempFile   = $_FILES['Filedata']['tmp_name'];
    $uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

    // Validate the filetype
    $fileParts = pathinfo($_FILES['Filedata']['name']);

    $photo_name = $_FILES['Filedata']['name'];
    $file_name = uniqid() . time() . rand(10000000, 99999999) . '.' . $fileParts['extension'];

    $targetFile = $uploadDir . $file_name;

    $result = array();

    if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

        // Save the file
        move_uploaded_file($tempFile, $targetFile);

        $result['file_description'] = $photo_name;
        $result['file_name'] = $file_name;
        $result['path'] = $targetFile;
        $result['size'] = ceil(filesize($targetFile) / 1024);
        $result['md5'] = md5_file($targetFile);
        $result['extension'] = $fileParts['extension'];
        $result['code'] = 200;
        $result['msg'] = 'ok';

        echo json_encode($result);

    } else {
        $result['code'] = 0;
        $result['msg'] = 'error: file extension error!';

        echo json_encode($result);
    }
}
?>