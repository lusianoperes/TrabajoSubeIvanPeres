<?php
namespace TrabajoSube;

class Boleto{

    public $costoViaje;
    public $saldoRestante;

    public function __construct($costo, $saldo) {
        
        $this->costoViaje = $costo;
        $this->saldoRestante = $saldo;

    }
}