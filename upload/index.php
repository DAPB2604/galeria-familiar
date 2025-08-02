<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\UploadedFile;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$apiKey = "123456"; // Token estático

// Middleware CORS
$app->add(function (Request $request, RequestHandlerInterface $handler): Response {
    $response = $handler->handle($request);

    // CORS headers
    $origin = $request->getHeaderLine('Origin') ?: '*';

    return $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', 'X-API-KEY, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Responder OPTIONS inmediatamente (antes de cualquier autenticación)
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Middleware de autenticación (excepto para '/')
$app->add(function (Request $request, RequestHandlerInterface $handler) use ($apiKey) {
    $path = $request->getUri()->getPath();

    // Permitir "/" y OPTIONS sin autenticación
    if ($path === '/' || $request->getMethod() === 'OPTIONS') {
        return $handler->handle($request);
    }

    $headerKey = $request->getHeaderLine('X-API-KEY');
    if ($headerKey !== $apiKey) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["error" => "Unauthorized"]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

// Ruta pública para verificar el estado
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(["status" => "API funcionando"]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta para subir imágenes
$app->post('/fotos', function (Request $request, Response $response) {
    $directory = __DIR__ . '/photos';
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $uploadedFiles = $request->getUploadedFiles();
    $foto = $uploadedFiles['foto'] ?? null;

    if ($foto instanceof UploadedFile && $foto->getError() === UPLOAD_ERR_OK) {
        $filename = $foto->getClientFilename();
        $foto->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        $response->getBody()->write(json_encode(["ok" => true, "file" => $filename]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(["error" => "Error al subir imagen"]));
    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
});

$app->run();
