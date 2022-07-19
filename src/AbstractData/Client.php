<?php
namespace Compdb\eFactura\AbstractData;

abstract class Client
{
    public string $denumire;
    public string $cif;
    public int $cifNumeric;
    public string $nrRecom;
    public string $codTara;
    public string $judet;
    public string $adresa1;
    public string $adresa2;
    public string $adresa3;
    public string $iban;
    public string $banca;
}
