<?php
require_once '/home/ore/unirest-php/lib/Unirest.php';

$response = Unirest::post("https://camfind.p.mashape.com/image_requests",
  array(
    "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk"
  ),
  array(
    "image_request[altitude]" => "27.912109375",
    "image_request[image]" => Unirest::file("download.png"),
    "image_request[language]" => "en",
    "image_request[locale]" => "en_US"
  )
);

$token = $response->body->token;
echo $token;


// These code snippets use an open-source library. http://unirest.io/php
$response = Unirest::get(trim("https://camfind.p.mashape.com/image_responses/".$token),
array(
    "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk"
)
);

$try=1; while ($response->body->status == 'not completed' && $try <=2){
    sleep(2);
    $response = Unirest::get( "https://camfind.p.mashape.com/image_responses/".$token,
        array(  
            "X-Mashape-Key" => "b7gwKEwrKWmshNp0pl6lOWFO774jp1K9HEPjsnupEdvqB0ikGk" 
        ) 
        ); }


echo "response status" . $response->body->status.PHP_EOL;
echo "response reason" . $response->body->reason.PHP_EOL;
echo "response name" . $response->body->name.PHP_EOL;


?>
