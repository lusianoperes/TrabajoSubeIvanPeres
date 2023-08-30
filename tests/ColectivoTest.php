<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{
     
    public function testPagarConTarjeta() {

    $colectivo = new Colectivo();
    $tarjeta = new Tarjeta();

    $saldosPosibles = Tarjeta::VALORESDECARGAPERMITIDOS;

    for ($i = 0; $i < count($saldosPosibles); $i++) {

        $tarjeta->saldo = $saldosPosibles[$i];
        $saldoPrePago = $tarjeta->saldo;

        ob_start(); //Empieza la lectura del buffer
        $boleto = $colectivo->pagarCon($tarjeta);
        $output = ob_get_clean(); //Termina y almacena lo recibido en el buffer

        if ($saldoPrePago >= Colectivo::TARIFABÁSICA) {

            $this->assertInstanceOf(Boleto::class, $boleto);

            $this->assertEquals(Colectivo::TARIFABÁSICA, $boleto->costoViaje);

            $this->assertEquals($tarjeta->saldo, $boleto->saldoRestante);

            }
        else {

            $this->assertNull($boleto);

            $expectedOutput = "Saldo Insuficiente. Tienes $" . $tarjeta->saldo . " en tu tarjeta";

            $this->assertEquals($output, $expectedOutput);

            }

        }

    }

    public function testCargaTarjeta() {

        $colectivo = new Colectivo();
        $tarjeta = new Tarjeta();

        $saldosPosibles = Tarjeta::VALORESDECARGAPERMITIDOS;

        $cargaRandom = [];
        for ($i = 0; $i < 100; $i++) {
            $cargaRandom[$i] = rand(0,6600);
        }

        $cargasNoPermitidas = array_diff($cargaRandom, $saldosPosibles);

        $datosDePrueba = array_merge($cargasNoPermitidas, $saldosPosibles);

        for ($i = 0; $i < count($datosDePrueba); $i++) {

            $tarjeta->saldo = $datosDePrueba[$i];
            $saldoPreCarga = $tarjeta->saldo;

            for ($j = 0; $j < count($datosDePrueba); $j++) {

                $tarjeta->saldo = $saldoPreCarga;

                ob_start(); //Empieza la lectura del buffer
                $tarjeta->cargarTarjeta($datosDePrueba[$j]);
                $output = ob_get_clean(); //Termina y almacena lo recibido en el buffer

                if (!in_array($datosDePrueba[$j], $saldosPosibles)) {

                    $expectedOutput = "La carga de $" . $datosDePrueba[$j] . "es inválida. Los valores disponibles de carga son: " . $saldosPosibles;

                }
                else if (($saldoPreCarga + $datosDePrueba[$j]) > Tarjeta::LIMITESALDO) {

                    $expectedOutput = "Carga Denegada. El límite de saldo de una tarjeta es de " . Tarjeta::LIMITESALDO;

                }
                else {

                    $expectedOutput = "Has cargado $" . $datosDePrueba[$j] . " en tu tarjeta. Tu saldo ahora es de: $" . $tarjeta->saldo;
                    $this->assertTrue(($saldoPreCarga + $datosDePrueba[$j]) == $tarjeta->saldo, "saldoprecarga: " . $saldoPreCarga . " carga: " . $datosDePrueba[$j] . " saldotarjetaactual: " . $tarjeta->saldo);

                }

                $this->assertEquals($output, $expectedOutput);

            }
        }
    }

}