<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require 'vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);
$container = $app->getContainer();

$container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, Exception $exception) use ($c) {
        $body = "Error: " . $exception->getMessage() . "\n\n" . $exception->getTraceAsString();
        $response->getBody()->write($body);
        return $response->withStatus(500)->withHeader('Content-Type', 'text/plain');
    };
};

$apiKey = "123456";

$app->add(function (Request $request, Response $response, callable $next) use ($apiKey) {
    $path = $request->getUri()->getPath();
    if ($path === '/') {
        return $next($request, $response);
    }
    $headerKey = $request->getHeaderLine('X-API-KEY');
    if ($headerKey !== $apiKey) {
        $data = json_encode(["error" => "Unauthorized"]);
        $response->getBody()->write($data);
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    return $next($request, $response);
});

$app->get('/', function (Request $req, Response $res) {
    $res->getBody()->write(json_encode(["status" => "API funcionando"]));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->post('/comentarios', function (Request $req, Response $res) {
    $body = $req->getBody()->getContents();
    $data = json_decode($body, true);

    if (empty($data['img']) || empty($data['comentario'])) {
        $res->getBody()->write(json_encode(["error" => "Faltan datos obligatorios"]));
        return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $linea = date("Y-m-d H:i:s") . " | " . $data['img'] . " | " . $data['comentario'] . PHP_EOL;
    file_put_contents("comentarios.txt", $linea, FILE_APPEND);

    $res->getBody()->write(json_encode(["ok" => true]));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->run();
