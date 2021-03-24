<?php

namespace App\Service;

class CalculPrixTTC
{

    private $tva;

    public function __construct($tva)
    {
        $this->tva= $tva;

    }
    public function calculerPrixTTC($prix)
    {
        return $prix + ($prix * $this->tva);
    }
}




?>