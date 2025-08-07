<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class FotosTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8001',
            'http_errors' => false,
        ]);
    }

    public function testSubirImagenExitosa(): void
    {
        $fotoPath = __DIR__ . '/Diego.jpg';

        // Asegúrate de que exista una imagen para subir
        $this->assertFileExists($fotoPath, "No se encuentra ejemplo.jpg en /tests");

        $response = $this->client->post('/fotos', [
            'headers' => [
                'X-API-KEY' => '123456',
            ],
            'multipart' => [
                [
                    'name'     => 'foto',
                    'contents' => fopen($fotoPath, 'r'),
                    'filename' => 'Diego.jpg'
                ]
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode(), "Código inesperado");
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('ok', $data);
        $this->assertTrue($data['ok']);
    }
}
