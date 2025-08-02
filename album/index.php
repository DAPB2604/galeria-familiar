<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$apiKey = "123456"; // tu token estático

// Crear app Slim 4
$app = AppFactory::create();

// Middleware para verificar el token API Key
$app->add(function (Request $request, $handler) use ($apiKey) {
    $headerKey = $request->getHeaderLine('X-API-KEY');
    if ($headerKey !== $apiKey) {
        $response = new \Slim\Psr7\Response();
        $data = ["error" => "Unauthorized"];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    return $handler->handle($request);
});

// Ruta para listar las fotos del álbum
$app->get('/albums', function (Request $request, Response $response) {
    $dir = __DIR__ . "/photos";
    $fotos = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $archivo) {
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $fotos[] = $archivo;
            }
        }
    }
    $response->getBody()->write(json_encode($fotos));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
