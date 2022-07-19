<?php
namespace Compdb\eFactura\AbstractData;

use DateTime;

abstract class Sumar
{
    public string $codClientFactura;
    public string $numarFactura;
    public DateTime $dataFactura;
    public DateTime $dataScadenta;
    public string $numarComanda;
    public string $numarComandaClient;
    public string $tipFactura;
    public string $deviz;
    public float $baza;
    public float $tva;
    public float $total;
}
