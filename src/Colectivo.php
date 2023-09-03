<?php
namespace TrabajoSube;

class Colectivo{
    
    public const TARIFABÁSICA = 120;

    public function pagarCon(Tarjeta $tarjeta) {

        if (($tarjeta->saldo - self::TARIFABÁSICA) >= 0) {

            $tarjeta->saldo =  $tarjeta->saldo - self::TARIFABÁSICA;

            $boleto = new Boleto(self::TARIFABÁSICA, $tarjeta->saldo);
            return $boleto;
        }
        else {

            return false;

        }

    }

}