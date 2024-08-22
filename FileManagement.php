<?php
header('Content-Type: application/json');  // Return JSON response
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$targetDirectory = __DIR__ . '/uploads/';  //save the files in the uploads folder
$filenamesFile = __DIR__ . '/filenames.txt';
$originalFileName = $_FILES["file"]["name"]; // Get the original file name
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

//isset checks whether the user entered anything in the input with the label "fileName"
$customFileName = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';
if ($customFileName != '' && $customFileName != ' ') {
    $fileNameWithoutExtension = $customFileName;
}

$targetFile = $targetDirectory . $fileNameWithoutExtension;

$uploadOk = 1;
$message = '';
$response = [];
// Check if the file is a valid upload
if ($_FILES["file"]["error"] > 0) {
    $message = "Error uploading file.";
    $uploadOk = 0;
}

// Check file size (limit to 2MB as an example)
if ($_FILES["file"]["size"] > 2097152) {
    $message = "Sorry, your file is too large.";
    $uploadOk = 0;
}

// If everything is ok, try to upload file
if ($uploadOk == 1) {
       if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)){
        $fileUrl = $targetFile ;
        $message = "The file " . basename($_FILES["file"]["name"]) . " has been uploaded.";
        $response = [
            'image_success' => true,
            'message' => $message,
            'type' => $originalFileName,
            'fileUrl' => $fileUrl,
            'name' => $fileNameWithoutExtension
        ];
        // ------------------------------new stuff-------------------------
            // get the json
            $jsonFile = 'filenames.json';
            //read the json and put its data in a variable
            $jsonData = json_decode(file_get_contents($jsonFile), true);

            $fileSplit = explode('.', $originalFileName);
            $typeOfFile = end($fileSplit);

            // create new json part
            $newFile = array(
                "name" => $fileNameWithoutExtension,
                "type" => $typeOfFile
                //will add alt text later if successful
            );
            // add new data to existing data
            $jsonData[] = $newFile;

            file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));



        //-----------------------------------------------------------------
//         $fileHandle = fopen($filenamesFile, 'a');  // Open file in append mode
//         if ($fileHandle) {
//             if ($customFileName != '' && $customFileName != ' ') {
//             fwrite($fileHandle, $customFileName . PHP_EOL);  // Write filename and new line
//             }else{
//             fwrite($fileHandle, $fileNameWithoutExtension . PHP_EOL);  // Write filename and new line
//             }
//             fclose($fileHandle);  // Close the file
//             }
} else {
        $message = "Sorry, there was an error uploading your file.";
    }
}

// Return message
echo json_encode($response);


?>
