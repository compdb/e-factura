<?php
namespace Compdb\eFactura\Raspuns;

use Sabre\Xml\Reader;
use Compdb\eFactura\AppException;

class Stare
{
    public ?string $stare;
    public ?int $index;
    public ?array $erori = null;

    const OK = 'ok';
    const EROARE = 'nok';
    const RESPINS = 'XML cu erori nepreluat de sistem';
    const PRELUCRARE = 'in prelucrare';

    public function __construct(string $xml)
    {
        $reader = new Reader();
        $reader->XML($xml);
        $data = $reader->parse();

        if (($data['name'] ?? '') !== '{mfp:anaf:dgti:efactura:stareMesajFactura:v1}header') {
            throw new AppException("Raspunsul stare nu contine header valid");
        }

        $this->stare = $data['attributes']['stare'] ?? null;
        $this->index = $data['attributes']['id_descarcare'] ?? null;

        if (is_array($data['value'])) {
            foreach($data['value'] as $content) {
                if (($content['name'] ?? '') !== '{mfp:anaf:dgti:efactura:stareMesajFactura:v1}Errors') {
                    throw new AppException("Raspunsul stare contine alte inregistrari decat erori standard");
                }
                $this->erori []= $content['attributes']['errorMessage'] ?? '';
            }
        }

        if (count(($this->erori ?? [])) === 0 && !in_array(($data['attributes']['stare'] ?? ''), [self::OK, self::EROARE, self::RESPINS, self::PRELUCRARE], true)) {
            throw new AppException("Raspunsul stare nu contine una dintre starile configurate");
        }
    }
}