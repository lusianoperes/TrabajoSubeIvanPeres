<?php

namespace TrabajoSube;

class Colectivo
{

    public const TARIFABÁSICA = 120;

    public $limiteSaldoNegativo = Tarjeta::LIMITESALDONEGATIVO / 100;

    public $lineaDeColectivo;

    public function __construct($linea = 1)
    {
        $this->lineaDeColectivo = $linea;
    }

    public function pagarCon(Tarjeta $tarjeta)
    {

        $horaactual = date("H:i");
        $diaActual = date('N');
        $horaInicio = '06:00';
        $horaFin = '22:00';
        $diaInicio = 1;
        $diaFin = 5;


        if ($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {

            if ($horaactual >= $horaInicio && $horaactual <= $horaFin && $diaActual >= $diaInicio && $diaActual <= $diaFin) {
                if (((strtotime($tarjeta->timer)) / 60) + 5 >= strtotime($horaactual)  && $tarjeta->timer != 0) {
                    return false;
                } 
                else 
                {

                    if ($tarjeta->ultimo != null && ($tarjeta->ultimo + 86400) >= strtotime($horaactual)) {
                        $tarjeta->viajes = 0;
                    }
                    if ($tarjeta->viajes <= 4) {
                        $monto = self::TARIFABÁSICA / 2;
                    } else {
                        $monto = self::TARIFABÁSICA;
                    }
                }
            } 
            else 
            {
                return false;
            }
        } else if ($tarjeta instanceof TarjetaJubilado) {

            if ($horaactual >= $horaInicio && $horaactual <= $horaFin && $diaActual >= $diaInicio && $diaActual <= $diaFin) {
                $monto = 0;
            }else{
                return false;
            }
            
        } else if ($tarjeta instanceof TarjetaEducativa) {

            if ($horaactual >= $horaInicio && $horaactual <= $horaFin && $diaActual >= $diaInicio && $diaActual <= $diaFin) {
                if ($tarjeta->ultimo != null && ($tarjeta->ultimo + 86400) >= strtotime($horaactual)) {
                    $tarjeta->viajes = 0;
                }
                if ($tarjeta->viajes < 2) {
                    $monto = 0;
                } else {
                    $monto = self::TARIFABÁSICA;
                }
            }else{
                return false;
            }
            
        } else {

            if ($tarjeta->ultimo != null && ($tarjeta->ultimo + 86400) >= strtotime($horaactual)) {
                $tarjeta->dias += 1;
            }
            if ($tarjeta->dias >= 1 && $tarjeta->dias <= 30) {

                $tarjeta->viajespormes = $tarjeta->viajespormes;
            } else {

                $tarjeta->viajespormes = 1;
            }

            switch ($tarjeta->viajespormes) {

                case ($tarjeta->viajespormes >= 1 && $tarjeta->viajespormes <= 29):
                    $monto = self::TARIFABÁSICA;
                    break;
                case ($tarjeta->viajespormes >= 30 && $tarjeta->viajespormes <= 79):
                    $monto = self::TARIFABÁSICA * 0.80;
                    break;
                default:
                    $monto = self::TARIFABÁSICA * 0.75;
                    break;
            }
        }

        if (($tarjeta->saldo - $monto) >= $this->limiteSaldoNegativo) {

            if ($tarjeta->deuda != 0) {

                if ($tarjeta->deuda < $tarjeta->saldo && $tarjeta->saldo - $tarjeta->deuda >= $monto) {

                    $tarjeta->saldo = $tarjeta->saldo - $tarjeta->deuda;
                    $deudaAux = $tarjeta->deuda;
                    $tarjeta->deuda = 0;
                    $tarjeta->saldo =  $tarjeta->saldo - $monto;
                    if ($tarjeta->saldo <= Tarjeta::LIMITESALDO && $tarjeta->exceso != 0) {
                        while ($tarjeta->exceso > 0 && $tarjeta->saldo < Tarjeta::LIMITESALDO) {
                            $tarjeta->saldo++;
                            $tarjeta->exceso--;
                        }
                    }
                    $boleto = new Boleto($monto, $tarjeta->saldo, null, $tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo, "Has abonado la deuda de: " . $deudaAux);
                    if ($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
                        $tarjeta->viajes += 1;
                        $tarjeta->timer = $horaactual;
                        $tarjeta->ultimo = $horaactual;
                    } else if ($tarjeta instanceof TarjetaEducativa) {

                        $tarjeta->viajes += 1;
                        $tarjeta->ultimo = $horaactual;
                    } else {
                        $tarjeta->viajespormes += 1;
                        $tarjeta->ultimo = $horaactual;
                    }
                    return $boleto;
                } else {
                    return false;
                }
            }

            $tarjeta->saldo =  $tarjeta->saldo - $monto;
            if ($tarjeta->saldo <= Tarjeta::LIMITESALDO && $tarjeta->exceso != 0) {
                while ($tarjeta->exceso > 0 && $tarjeta->saldo < Tarjeta::LIMITESALDO) {
                    $tarjeta->saldo++;
                    $tarjeta->exceso--;
                }
            }
            $boleto = new Boleto($monto, $tarjeta->saldo, null, $tarjeta->ID, $tarjeta->tipoDeTarjeta, $this->lineaDeColectivo);
            if ($tarjeta instanceof TarjetaEstudiantil || $tarjeta instanceof TarjetaUniversitaria) {
                $tarjeta->viajes += 1;
                $tarjeta->timer = $horaactual;
                $tarjeta->ultimo = $horaactual;
            } else if ($tarjeta instanceof TarjetaEducativa) {

                $tarjeta->viajes += 1;
                $tarjeta->ultimo = $horaactual;
            } else {
                $tarjeta->viajespormes += 1;
                $tarjeta->ultimo = $horaactual;
            }
            return $boleto;
        } else {

            return false;
        }
    }

}

class ColectivoInterUrbano extends Colectivo{

    public const TARIFABÁSICA = 1;

}
