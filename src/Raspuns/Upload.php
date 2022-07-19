<?php
namespace Compdb\eFactura\Raspuns;

use Sabre\Xml\Reader;
use Compdb\eFactura\AppException;

class Upload
{
    public ?string $data;
    public ?int $stare;
    public ?int $index;
    public ?array $erori = null;

    public function __construct(string $xml)
    {
        $reader = new Reader();
        $reader->XML($xml);
        $data = $reader->parse();

        if (($data['name'] ?? '') !== '{mfp:anaf:dgti:spv:respUploadFisier:v1}header') {
            throw new AppException("Raspunsul upload nu contine header valid");
        }

        $this->data = $data['attributes']['dateResponse'] ?? null;
        $this->stare = $data['attributes']['ExecutionStatus'] ?? null;
        $this->index = $data['attributes']['index_incarcare'] ?? null;
        if (is_array($data['value'])) {
            foreach($data['value'] as $content) {
                if (($content['name'] ?? '') !== '{mfp:anaf:dgti:spv:respUploadFisier:v1}Errors') {
                    throw new AppException("Raspunsul upload contine alte inregistrari decat erori standard");
                }
                $this->erori []= $content['attributes']['errorMessage'] ?? '';
            }
        }
    }
}
