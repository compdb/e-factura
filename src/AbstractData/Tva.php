<?php
namespace Compdb\eFactura\AbstractData;

abstract class Tva
{
    public string $categorie;
    public float $procent;
    public float $baza;
    public float $tva;
}
