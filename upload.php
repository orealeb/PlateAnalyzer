<?php
require_once '/home/alebios2/public_html/upload2/unirest-php/lib/Unirest.php';

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        echo "<br>";
        
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        echo "<br>";
        
        $uploadOk = 0;
    }
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    echo "<br>";
    
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    echo "<br>";
    
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
        echo "<br>";
        
        echo $target_file;
        echo "<br><br>";
        echo "CamFind API Call";
        echo "<br>";
        
        $response = Unirest::post("https://camfind.p.mashape.com/image_requests", array(
            "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk"
        ), array(
            "image_request[altitude]" => "27.912109375",
            "image_request[image]" => Unirest::file(trim($target_file)),
            "image_request[language]" => "en",
            "image_request[locale]" => "en_US"
        ));
        
        $token = $response->body->token;
        echo "Token {$token}";
        echo "<br>";
        
        // These code snippets use an open-source library. http://unirest.io/php
        $response = Unirest::get(trim("https://camfind.p.mashape.com/image_responses/" . $token), array(
            "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk"
        ));
        
        $try = 1;
        while ($response->body->status == 'not completed' && $try <= 2) {
            sleep(2);
            $response = Unirest::get("https://camfind.p.mashape.com/image_responses/" . $token, array(
                "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk"
            ));
        }
        
        echo "response status " . $response->body->status . PHP_EOL;
        echo "<br>";
        
        echo "response reason " . $response->body->reason . PHP_EOL;
        echo "<br>";
        
        echo "response name " . $response->body->name . PHP_EOL;
        echo "<br><br>";
        
        echo "Nutritionix API call";
        echo "<br>";
        
        $food = $response->body->name;
        $food = str_replace(' ', '%20', $food);
        
        $response = file_get_contents("https://api.nutritionix.com/v1_1/search/{$food}?fields=item_name%2Citem_id%2Cbrand_name%2Cnf_calories%2Cnf_total_fat&appId=80fbbce8&appKey=f00f0559be1828bf7113ee64afcfa9f6");
        
        $response = json_decode($response, true);
        
        echo "response total_hits " . $response['total_hits'] . PHP_EOL;
        echo "<br>";
        
        echo "response index " . $response['hits'][0]['_index'] . PHP_EOL;
        echo "<br>";
        
        echo "response item_id " . $response['hits'][0]['fields']['item_id'];
        echo "<br>";
        
        echo "response item_name " . $response['hits'][0]['fields']['item_name'] . PHP_EOL;
        echo "<br>";
        
        echo "response brand_name " . $response['hits'][0]['fields']['brand_name'] . PHP_EOL;
        echo "<br>";
        
        echo "response nf_calories " . $response['hits'][0]['fields']['nf_calories'] . PHP_EOL;
        echo "<br>";
        
        echo "response nf_total_fat " . $response['hits'][0]['fields']['nf_total_fat'] . PHP_EOL;
        echo "<br>";
    } 

    else {
        echo "Sorry, there was an error uploading your file.";
        echo "<br>";
    }
}

?>

