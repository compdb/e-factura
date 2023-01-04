<?php
namespace Compdb\eFactura;

use Compdb\UBL;

use DOMDocument;
use StdClass;

class Factura {

    private DOMDocument $dom; // DOMDocument Factura XML

    public function __construct($data) { //Data

        # Generare XML

        $furnizor = new StdClass();
        $furnizor->LegalEntity = (new UBL\LegalEntity())
            ->setRegistrationName($data->getFurnizor()->denumire)
            ->setCompanyID($data->getFurnizor()->cif)
            ->setCompanyLegalForm($data->getFurnizor()->informatiiSuplimentare);

        $furnizor->address = (new UBL\Address())
            ->setStreetName($data->getFurnizor()->adresa1)
            ->setCityName($data->getFurnizor()->numeLocalitate)
            ->addAddressLine($data->getFurnizor()->adresa1)
            ->addAddressLine($data->getFurnizor()->adresa2)
            ->addAddressLine($data->getFurnizor()->adresa3)
            ->setCountrySubentity($data->getFurnizor()->judet)
            ->setCountry((new UBL\Country())->setIdentificationCode($data->getFurnizor()->codTara));

        $furnizor->contact = (new UBL\Contact())
            ->setElectronicMail($data->getFurnizor()->email)
            ->setTelephone($data->getFurnizor()->telefon);

        $furnizor->Party = (new UBL\Party())
            ->setName($furnizor->LegalEntity->getRegistrationName())
            ->setLegalEntity($furnizor->LegalEntity)
            ->setPostalAddress($furnizor->address)
            ->setContact($furnizor->contact)
            ->setPartyTaxScheme((new UBL\PartyTaxScheme())
                ->setCompanyId($data->getFurnizor()->cif)
                ->setTaxScheme((new UBL\TaxScheme())->setId('VAT'))
            );

        $plata = (new UBL\PaymentMeans())
            ->setPaymentMeansCode(30) // UNCE 4461 30 Credit transfer
            ->setPayeeFinancialAccount((new UBL\FinancialAccount())
                ->setId($data->getFurnizor()->iban)
                ->setFinancialInstitutionBranch((new UBL\FinancialInstitutionBranch())
                    ->setId($data->getFurnizor()->banca)
                )
            );

        $client = new \StdClass();

        $client->LegalEntity = (new UBL\LegalEntity())
            ->setRegistrationName($data->getClient()->denumire)
            ->setCompanyID($data->getClient()->cif);
    
        $client->address = (new UBL\Address())
            ->setStreetName($data->getClient()->adresa1)
            ->addAddressLine($data->getClient()->adresa1)
            ->addAddressLine($data->getClient()->adresa2)
            ->setCountry((new UBL\Country())->setIdentificationCode($data->getClient()->codTara));

        if (trim($data->getClient()->adresa3)) {
            $client->address->addAddressLine($data->getClient()->adresa3);
        }

        if ($data->getClient()->judet) {
            $client->address->setCountrySubentity($data->getClient()->judet);
        }

        if ($data->getClient()->numeLocalitate) {
            $client->address->setCityName($data->getClient()->numeLocalitate);
        }
        else {
            $client->address->setCityName($data->getClient()->adresa2);
        }

        $client->Party = (new UBL\Party())
            ->setName($client->LegalEntity->getRegistrationName())
            ->setLegalEntity($client->LegalEntity)
            ->setPostalAddress($client->address);

        $linii = [];

        foreach ($data->getLinii() as $l) {
            $item = (new UBL\Item())
                ->setName($l->descriereArticol)
                ->addClassifiedTaxCategory((new UBL\ClassifiedTaxCategory())
                    ->setId($l->categorieTva)
                    ->setPercent($l->procentTva)
                    ->setTaxScheme((new UBL\TaxScheme())->setId('VAT'))
                );

            if (trim($l->codNomenclator)) {
                $item->addCommodityClassification((new UBL\CommodityClassification())
                    ->setItemClassificationCode($l->codNomenclator, ['listID' => $l->idNomenclator])
                );
            }

            if (trim($l->codArticol)) {
                $item->setSellersItemIdentification($l->codArticol);
            }
            if (trim($l->codArticolClient)) {
                $item->setBuyersItemIdentification($l->codArticolClient);
            }

            $xmlLinie = (new UBL\InvoiceLine())
                ->setId($l->index)
                ->setInvoicedQuantity($l->cantitateLivrata)
                ->setUnitCode($l->unitateMasura)
                ->setLineExtensionAmount($l->valoare)
                ->setItem($item)
                ->setPrice((new UBL\Price())
                    ->setPriceAmount($l->pret)
                );

            if ($l->text) {
                $xmlLinie->addNote($text);
            }

            $linii []= $xmlLinie;
        }

        $total = (new UBL\LegalMonetaryTotal())
            ->setLineExtensionAmount($data->getSumar()->baza)
            ->setTaxExclusiveAmount($data->getSumar()->baza)
            ->setTaxInclusiveAmount($data->getSumar()->total)
            ->setPayableAmount($data->getSumar()->total);

        $tva = (new UBL\TaxTotal())->setTaxAmount($data->getSumar()->tva);

        foreach ($data->getTva() as $dateTva) {
            $tva->addTaxSubTotal((new UBL\TaxSubTotal())
                ->setTaxableAmount($dateTva->baza)
                ->setTaxAmount($dateTva->tva)
                ->setTaxCategory((new UBL\TaxCategory())
                    ->setId($dateTva->categorie)
                    ->setPercent($dateTva->procent)
                    ->setTaxScheme((new UBL\TaxScheme())->setId('VAT'))
                )
            );
        }

        $factura = (new UBL\Invoice())
            ->setUBLVersionID('2.1')
            ->setCustomizationID('urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1')
            ->setInvoiceTypeCode($data->getSumar()->tipFactura)
            ->setId($data->getSumar()->numarFactura)
            ->setIssueDate($data->getSumar()->dataFactura)
            ->setDueDate($data->getSumar()->dataScadenta)
            ->setDocumentCurrencyCode($data->getSumar()->deviz)
            ->setAccountingSupplierParty($furnizor->Party)
            ->setAccountingCustomerParty($client->Party)
            ->setPaymentMeans($plata)
            ->addInvoiceLines($linii)
            ->setLegalMonetaryTotal($total)
            ->setTaxTotal($tva);

        foreach ($data->getText() as $text) {
            $factura->addNote($text);
        }

        $xmlString = (new UBL\Generator())->invoice($factura, $data->getSumar()->deviz);

        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        $this->dom->loadXML($xmlString);
        $this->dom->encoding = "utf-8";
    }

    public function getDOM()
    {
        return $this->dom;
    }
}
