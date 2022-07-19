<?php
namespace Compdb\eFactura;
use Compdb\eFactura\AppException;

class Token
{
    private object $token;

    public function __construct(string $filePath)
    {
        try {
            $this->token = json_decode(file_get_contents($filePath));
        } catch (Exception $e) {
            throw new AppException('Tokenul de autentificare nu a putut fi incarcat');
        }
    }

    public function info()
    {
        return $this->token;
    }

    public function access()
    {
        return $this->token->access_token;
    }
}