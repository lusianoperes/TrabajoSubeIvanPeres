<?php

namespace TrabajoSube;

class Colectivo{
    
    public const TARIFABÁSICA = 120;

    public $limiteSaldoNegativo = Tarjeta::LIMITESALDONEGATIVO / 100;

    public $lineaDeColectivo;

    public function __construct($linea = 1) {

        $this->lineaDeColectivo = $linea;

    }

    public function pagarCon(Tarjeta $tarjeta) {

        if($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
            
            $horaactual = date("H:i");
            if(((strtotime($tarjeta->timer)) / 60) + 5 >= strtotime($horaactual)  && $tarjeta->timer != 0)
            {
               return false;
            }
            else{
                 
                if($tarjeta->ultimo != null && (strtotime($tarjeta->ultimo) + 86400) == $horaactual)
                {
                    $tarjeta->viajes = 0;
                }
                if($tarjeta->viajes <= 4)
                {
                    $monto = self::TARIFABÁSICA / 2;
                }else{
                    $monto = self::TARIFABÁSICA;
                }

            }
        }
        else if($tarjeta instanceof TarjetaJubilado) {
            $monto = 0; 
        }
        else {
            $monto = self::TARIFABÁSICA;
        }
        
        if (($tarjeta->saldo - $monto) >= $this->limiteSaldoNegativo) {

            if($tarjeta->deuda != 0) {

                if($tarjeta->deuda < $tarjeta->saldo && $tarjeta->saldo - $tarjeta->deuda >= $monto) {

                    $tarjeta->saldo = $tarjeta->saldo - $tarjeta->deuda;
                    $deudaAux = $tarjeta->deuda;
                    $tarjeta->deuda = 0;
                    $tarjeta->saldo =  $tarjeta->saldo - $monto;
                    $boleto = new Boleto($monto, $tarjeta->saldo, null, $tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo, "Has abonado la deuda de: " . $deudaAux);
                    if($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
                        $tarjeta->viajes += 1;
                        $tarjeta->timer = $horaactual;
                        $tarjeta->ultimo = $horaactual;
                    }
                    return $boleto;
                }
                else{
                    return false;
                }

            }

            $tarjeta->saldo =  $tarjeta->saldo - $monto;
            $boleto = new Boleto($monto, $tarjeta->saldo, null ,$tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo);
            if($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
                $tarjeta->viajes += 1;
                $tarjeta->timer = $horaactual;
                $tarjeta->timer = $horaactual;
            }
            return $boleto;
        }
        else {

            return false;

        }

    }

}
