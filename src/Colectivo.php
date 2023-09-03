<?php
namespace TrabajoSube;

class Colectivo{
    
    public const TARIFABÁSICA = 120;

    public $limiteSaldoNegativo = Tarjeta::LIMITESALDONEGATIVO / 100;

    public function pagarCon(Tarjeta $tarjeta) {

        if (($tarjeta->saldo - self::TARIFABÁSICA) >= $limiteSaldoNegativo) {

            $tarjeta->saldo =  $tarjeta->saldo - self::TARIFABÁSICA;

            $boleto = new Boleto(self::TARIFABÁSICA, $tarjeta->saldo);
            return $boleto;
        }
        else {

            return false;

        }

    }

}