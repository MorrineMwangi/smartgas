<?php
require 'vendor/autoload.php';
require_once('./Cloudant.php');
$app = new \Slim\Slim();
$dotenv = new Dotenv\Dotenv(__DIR__);
try {
  $dotenv->load();
} catch (Exception $e) {
    error_log("No .env file found");
 }
$app->get('/', function () {
  global $app;
    $app->render('index.html');
});
$app->get('/', function () {
  global $app;
    $app->render('product.html');
});

$app->get('/api/visitors', function () {
  global $app;
  $app->contentType('application/json');
  $visitors = array();
  if(Cloudant::Instance()->isConnected()) {
    $visitors = Cloudant::Instance()->get();
  }
  echo json_encode($visitors);
});

$app->post('/api/visitors', function() {
  global $app;
    error_log("POST in /api/visitors");
  $app->contentType('application/json');
  $visitor = $app->request()->getBody();
  #$visitor = json_decode($app->request()->getBody(), true);
  if(Cloudant::Instance()->isConnected()) {
    $doc = Cloudant::Instance()->post($visitor);
    error_log("POST error: $visitor $doc");
    echo $doc;
    #echo json_encode($doc);
  } else {
    error_log("POST error: $visitor");
    echo json_encode($visitor);
  }
});

$app->delete('/api/visitors/:id', function($id) {
	global $app;
	Cloudant::Instance()->delete($id);
    $app->response()->status(204);
});

$app->put('/api/visitors/:id', function($id) {
	global $app;
	$visitor = json_decode($app->request()->getBody(), true);
    echo json_encode(Cloudant::Instance()->put($id, $visitor));
});

$app->run();
