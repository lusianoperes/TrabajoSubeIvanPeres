<?php
namespace TrabajoSube;

class Boleto{

    public $fecha;
    public $costoViaje;
    public $saldoRestante;
    public $tarjetaID;
    public $tipoDeTarjeta;
    public $lineaDeColectivo;
    public $mensaje;

    public function __construct($costo, $saldo, $Fecha, $ID, $tipo, $linea, $sms = "") {
        
        $this->costoViaje = $costo;
        $this->saldoRestante = $saldo;
        $this->fecha = $Fecha;
        $this->tarjetaID = $ID;
        $this->tipoDeTarjeta = $tipo;
        $this->lineaDeColectivo = $linea;
        $this->mensaje = $sms;
    }

    public function obtenerFecha() {

        return $this->fecha;
    }
    public function obtenerCostoViaje() {

        return $this->costoViaje;
    }
    public function obtenerSaldoRestante() {

        return $this->saldoRestante;
    }
    public function obtenerID() {

        return $this->tarjetaID;
    }
    public function obtenerTipoDeTarjeta() {

        return $this->tipoDeTarjeta;
    }
    public function obtenerLinea() {

        return $this->lineaDeColectivo;
    }
}