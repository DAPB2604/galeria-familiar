<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require 'vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

$app = new \Slim\App();

$apiKey = "123456"; // tu token estático

// Manejo personalizado de errores para mostrar detalles
$container = $app->getContainer();
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $body = "Error: " . $exception->getMessage() . "\n\n" . $exception->getTraceAsString();
        return $response->withStatus(500)
                        ->withHeader('Content-Type', 'text/plain')
                        ->write($body);
    };
};

// Ruta pública para probar si la API está viva (no requiere token)
$app->get('/', function ($req, $res) {
    $res->getBody()->write(json_encode(["status" => "API funcionando"]));
    return $res->withHeader('Content-Type', 'application/json');
});

// Middleware de seguridad para las demás rutas
$app->add(function ($request, $response, $next) use ($apiKey) {
    $path = $request->getUri()->getPath();
    if ($path === '/') {
        // No validar token para la raíz
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

$app->post('/fotos', function ($req, $res) {
    $uploadedFiles = $req->getUploadedFiles();
    $foto = $uploadedFiles['foto'] ?? null;

    if ($foto && $foto->getError() === UPLOAD_ERR_OK) {
        $filename = $foto->getClientFilename();
        $foto->moveTo("photos/$filename");
        return $res->withJson(["ok" => true, "file" => $filename]);
    }

    return $res->withStatus(400)->write("Error al subir imagen");
});

$app->run();
