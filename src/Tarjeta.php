<?php
namespace TrabajoSube;


class Tarjeta{

    public const LIMITESALDO = 6600;
    public const LIMITESALDONEGATIVO = -21184;
    public const VALORESDECARGAPERMITIDOS = [150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500, 4000];
    
    public $ID;
    public $saldo;
    public $deuda;
    public $tipoDeTarjeta;
    public $timer;

    public function __construct($id = 1, $sald = 0, $deu = 0) {
        $this->ID = $id;
        $this->saldo = $sald;
        $this->deuda = $deu;
        $this->tipoDeTarjeta = "Normal";
        $this->timer = null;
    }

    public function cargarTarjeta($carga) {

        if (in_array($carga, self::VALORESDECARGAPERMITIDOS)) {

            if(($this->saldo + $carga) <= self::LIMITESALDO) {

                    $this->saldo += $carga;

                echo "Has cargado $" . $carga . " en tu tarjeta. Tu saldo ahora es de: $" . $this->saldo;

            }
            else {

                echo "Carga Denegada. El límite de saldo de una tarjeta es de " . self::LIMITESALDO;

            }

        }
        else {

            echo "La carga de $" . $carga . "es inválida. Los valores disponibles de carga son: " . self::VALORESDECARGAPERMITIDOS;

        }
    }
}

class TarjetaEstudiantil extends Tarjeta{
    
    public function __construct($id = 1) {
        parent::__construct($id);
        $this->tipoDeTarjeta = "Estudiantil";
        $this->timer = 0;
    }
}

class TarjetaUniversitaria extends Tarjeta{

    public function __construct($id = 1) {
        parent::__construct($id);
        $this->tipoDeTarjeta = "Universitaria";
        $this->timer = 0;
    }
}

class TarjetaJubilado extends Tarjeta{

    public function __construct($id = 1) {
        parent::__construct($id);
        $this->tipoDeTarjeta = "Jubilado";
    }
}
