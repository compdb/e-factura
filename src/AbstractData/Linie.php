<?php
namespace Compdb\eFactura\AbstractData;

abstract class Linie
{
    public int $index;
    public ?string $numarComanda;
    public ?string $numarLinie;
    public ?string $codArticol;
    public string $descriereArticol;
    public ?string $codNomenclator;
    public ?string $codArticolClient;
    public float $pret;
    public float $valoare;
    public float $cantitateLivrata;
    public string $unitateMasura;
    public string $categorieTva;
    public float $procentTva;
    public ?string $text;
}
