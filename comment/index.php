<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpInternalServerErrorException;

$apiKey = "123456";

// Crear app Slim 4
$app = AppFactory::create();

// Middleware para manejo de errores (global)
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Middleware para autenticación API Key
$app->add(function (Request $request, $handler) use ($apiKey) {
    $path = $request->getUri()->getPath();
    if ($path === '/') {
        return $handler->handle($request);
    }

    $headerKey = $request->getHeaderLine('X-API-KEY');
    if ($headerKey !== $apiKey) {
        $response = new \Slim\Psr7\Response();
        $data = ["error" => "Unauthorized"];
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    return $handler->handle($request);
});

// Ruta raíz pública
$app->get('/', function (Request $req, Response $res) {
    $res->getBody()->write(json_encode(["status" => "API funcionando"]));
    return $res->withHeader('Content-Type', 'application/json');
});

// Ruta POST para comentarios
$app->post('/comentarios', function (Request $req, Response $res) {
    $body = $req->getBody()->getContents();
    $data = json_decode($body, true);

    if (empty($data['img']) || empty($data['comentario'])) {
        $res->getBody()->write(json_encode(["error" => "Faltan datos obligatorios"]));
        return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $linea = date("Y-m-d H:i:s") . " | " . $data['img'] . " | " . $data['comentario'] . PHP_EOL;
    $file = __DIR__ . "/comentarios.txt";

    // Intentar escribir en el archivo, si falla lanzar excepción
    if (false === file_put_contents($file, $linea, FILE_APPEND | LOCK_EX)) {
        throw new HttpInternalServerErrorException($req, "No se pudo guardar el comentario");
    }

    $res->getBody()->write(json_encode(["ok" => true]));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->run();
