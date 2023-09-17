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
            $monto = self::TARIFABÁSICA / 2; 
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

                    $tarjeta->saldo -= $tarjeta->deuda;
                    $deudaAux = $tarjeta->deuda;
                    $tarjeta->deuda -= 0;
                    $tarjeta->saldo =  $tarjeta->saldo - $monto;
                    $fecha = date('Y-m-d');
                    $boleto = new Boleto($monto, $tarjeta->saldo, $fecha, $tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo, "Has abonado la deuda de: " . $deudaAux);
                    return $boleto;

                }

            }

            $tarjeta->saldo =  $tarjeta->saldo - $monto;
            $fecha = date('Y-m-d'); 
            $boleto = new Boleto($monto, $tarjeta->saldo, $fecha, $tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo);
            return $boleto;
        }
        else {

            return false;

        }

    }

}
