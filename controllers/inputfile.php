<?php

function uploadFile($fileInputName, $uploadDir)
{
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === 0) {
        // Menyusun nama file yang unik
        $fileName = uniqid() . '-' . basename($_FILES[$fileInputName]['name']);
        $filePath = $uploadDir . $fileName;

        // Mengupload file ke server
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filePath)) {
            return 'uploads/' . $fileName;
        } else {
            throw new Exception("Gagal mengunggah file $fileInputName.");
        }
    }
    return null;
}
