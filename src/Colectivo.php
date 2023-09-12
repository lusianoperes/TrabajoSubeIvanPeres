<?php
namespace TrabajoSube;

class Colectivo{
    
    public const TARIFABÁSICA = 120;

    public $limiteSaldoNegativo = Tarjeta::LIMITESALDONEGATIVO / 100;

    public function pagarCon(Tarjeta $tarjeta) {

        if($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
            $monto = self::TARIFABÁSICA / 2; 
        }
        else if($tarjeta instanceof TarjetaJubilado) {
            $monto = 0; 
        }
        else {
            $monto = self::TARIFABÁSICA;
        }
        
        if (($tarjeta->saldo - $monto) >= $this->limiteSaldoNegativo) {

            if ($tarjeta->saldo < $monto) {

                $tarjeta->deuda = $monto - $tarjeta->saldo;
            }

            $tarjeta->saldo =  $tarjeta->saldo - $monto;

            $boleto = new Boleto($monto, $tarjeta->saldo);
            return $boleto;
        }
        else {

            return false;

        }

    }

}
