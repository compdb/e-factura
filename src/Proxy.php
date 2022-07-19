<?php
namespace Compdb\eFactura;

class Proxy
{
    private string $token;
    private array $url = [
        'upload' => 'https://api.anaf.ro/prod/FCTEL/rest/upload',
        'listaMesaje' => 'https://api.anaf.ro/prod/FCTEL/rest/listaMesajeFactura',
        'stareMesaj' => 'https://api.anaf.ro/prod/FCTEL/rest/stareMesaj',
        'descarcare' => 'https://api.anaf.ro/prod/FCTEL/rest/descarcare',
    ];

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function upload (int $cif, string $XMLString, string $standard = 'UBL')
    {
        $url = $this->url['upload'] . '?' . http_build_query([
            'standard' => $standard,
            'cif' => $cif
        ]);
        $context = $this->streamContext($XMLString);
        return $this->read($url, $context);
    }

    public function listaMesaje (int $cif, int $zile = 60)
    {
        $url = $this->url['listaMesaje'] . '?' . http_build_query([
            'zile' => $zile,
            'cif' => $cif
        ]);
        $context = $this->streamContext();
        return $this->read($url, $context);
    }

    public function stareMesaj (int $indexIncarcare)
    {
        $url = $this->url['stareMesaj'] . '?' . http_build_query([
            'id_incarcare' => $indexIncarcare
        ]);
        $context = $this->streamContext();
        return $this->read($url, $context);
    }

    public function descarcare (int $indexDescarcare)
    {
        $url = $this->url['descarcare'] . '?' . http_build_query([
            'id' => $indexDescarcare
        ]);
        $context = $this->streamContext();
        return $this->read($url, $context);
    }

    private function streamOptions()
    {
        return [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->token
            ]
        ];
    }

    private function streamContext(string $postXMLString = '')
    {
        $options = $this->streamOptions();
        if ($postXMLString != '') {
            $options['http']['method'] = 'POST';
            $options['http']['header'] .= "\r\n";
            $options['http']['header'] .= 'Content-type: text/plain';
            $options['http']['content'] = $postXMLString;
        }
        return stream_context_create($options);
    }

    private function read (string $url, $context)
    {
        $stream = fopen($url, 'r', false, $context);
        $contents = stream_get_contents($stream);
        fclose($stream);
        return $contents;
    }
}
