<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require 'vendor/autoload.php';

$apiKey = "123456"; // tu token estático

$app = new \Slim\App();

// Middleware para verificar el token (Slim 3 usa $request, $response, $next)
$app->add(function ($request, $response, $next) use ($apiKey) {
    $headerKey = $request->getHeaderLine('X-API-KEY');
    if ($headerKey !== $apiKey) {
        $data = ["error" => "Unauthorized"];
        return $response->withStatus(401)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    }
    $response = $next($request, $response);
    return $response;
});

// Ruta para listar las fotos del álbum
$app->get('/albums', function ($request, $response) {
    $dir = "photos";
    $fotos = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $archivo) {
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $fotos[] = $archivo;
            }
        }
    }
    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($fotos));
});

$app->run();
