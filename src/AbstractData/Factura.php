<?php
namespace Compdb\eFactura\AbstractData;

abstract class Factura
{
    protected Sumar $sumar;
    protected Furnizor $furnizor;
    protected Client $client;
    protected array $tva;
    protected array $linii;
    protected array $text;

    public function __construct(Sumar $sumar, Furnizor $furnizor, Client $client, array $tva, array $linii, ?array $text)
    {

        $this->sumar = $sumar;
        $this->furnizor = $furnizor;
        $this->client = $client;
        $this->tva = $this->addTva(...$tva);
        $this->linii = $this->addLinii(...$linii);
        $this->text = $this->addText(...$text);
    }

    public function getSumar(): Sumar
    {
        return $this->sumar;
    }

    public function setSumar(Sumar $sumar): Factura
    {
        $this->sumar = $sumar;
        return $this;
    }

    public function getFurnizor(): Furnizor
    {
        return $this->furnizor;
    }

    public function setFurnizor(Furnizor $furnizor): Factura
    {
        $this->furnizor = $furnizor;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Factura
    {
        $this->client = $client;
        return $this;
    }

    public function getTva(): array
    {
        return $this->tva;
    }

    public function addTva(Tva ...$liniiTVA): Factura
    {
        $this->tva = $liniiTVA;
        return $this;
    }

    public function getLinii(): array
    {
        return $this->linii;
    }

    public function addLinii(Linie ...$linii): Factura
    {
        $this->linii = $linii;
        return $this;
    }

    public function getText(): array
    {
        return $this->text;
    }

    public function addText(string ...$liniiText): Factura
    {
        $this->text = $liniiText;
        return $this;
    }
}
