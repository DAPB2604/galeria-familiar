<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require 'vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;
$app = new \Slim\App();

$apiKey = "123456"; // tu token estático

// Middleware para CORS y autenticación
$app->add(function ($request, $response, $next) use ($apiKey) {
    // Responder rápido a OPTIONS (preflight)
    if ($request->getMethod() === 'OPTIONS') {
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-API-KEY')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withStatus(200);
    }

    // Para otras peticiones, verificar API Key salvo para ruta '/'
    $path = $request->getUri()->getPath();
    if ($path !== '/') {
        $headerKey = $request->getHeaderLine('X-API-KEY');
        if ($headerKey !== $apiKey) {
            $data = json_encode(["error" => "Unauthorized"]);
            $response->getBody()->write($data);
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-API-KEY')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
    }

    // Continuar con el siguiente middleware/ruta
    $response = $next($request, $response);

    // Agregar headers CORS a todas las respuestas
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-API-KEY')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


// Ruta pública para verificar API
$app->get('/', function ($req, $res) {
    $res->getBody()->write(json_encode(["status" => "API funcionando"]));
    return $res->withHeader('Content-Type', 'application/json');
});

// Ruta POST para subir imagen
$app->post('/fotos', function ($req, $res) {
    $uploadedFiles = $req->getUploadedFiles();
    $foto = $uploadedFiles['foto'] ?? null;

    if ($foto && $foto->getError() === UPLOAD_ERR_OK) {
        $filename = $foto->getClientFilename();
        $foto->moveTo("photos/$filename");
        return $res->withJson([
            "ok" => true,
            "file" => $filename,
            "path" => "photos/$filename"
        ]);
    }

    return $res->withStatus(400)->write("Error al subir imagen");
});

$app->run();

