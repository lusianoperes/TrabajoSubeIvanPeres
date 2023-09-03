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

        $retorno = $colectivo->pagarCon($tarjeta);

        if ($saldoPrePago >= Colectivo::TARIFABÁSICA) {

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals(Colectivo::TARIFABÁSICA, $retorno->costoViaje);

            $this->assertEquals($tarjeta->saldo, $retorno->saldoRestante);

            }
        else {

            $expectedOutput = false;

            $this->assertEquals($retorno, $expectedOutput);

            }

        }

    }

    public function testCargaTarjeta() {

        $colectivo = new Colectivo();
        $tarjeta = new Tarjeta();

        $saldosPosibles = Tarjeta::VALORESDECARGAPERMITIDOS;

        //Probar todas las cargas válidas con saldo de tarjeta en 0
        for ($i = 0; $i < count($saldosPosibles); $i++)
        {

            $tarjeta->saldo = 0;
            ob_start(); 
            $tarjeta->cargarTarjeta($saldosPosibles[$i]);
            $output = ob_get_clean(); 

            $expectedOutput = "Has cargado $" . $saldosPosibles[$i] . " en tu tarjeta. Tu saldo ahora es de: $" . $tarjeta->saldo;

            $this->assertEquals(($saldosPosibles[$i]), $tarjeta->saldo);
            $this->assertEquals($output, $expectedOutput);
            
        }

        //Probar cargas invalidas con saldo de tarjeta en 0
        $cargasNoValidas = [1, 9000, 25, 120];
        for ($i = 0; $i < count($cargasNoValidas); $i++)
        {

            $tarjeta->saldo = 0;
            ob_start(); 
            $tarjeta->cargarTarjeta($cargasNoValidas[$i]);
            $output = ob_get_clean(); 

            $expectedOutput = "La carga de $" . $cargasNoValidas[$i] . "es inválida. Los valores disponibles de carga son: " . $saldosPosibles;

            $this->assertEquals(0, $tarjeta->saldo);
            $this->assertEquals($output, $expectedOutput);
            
        }

        //Probar pasarse de saldo
        $saldoPorPasarse = 5000;
        $cargasValidasParaPasarse = [3000, 4000, 2000];
        for ($i = 0; $i < count($cargasValidasParaPasarse); $i++)
        {
            
            $tarjeta->saldo = $saldoPorPasarse;
            ob_start(); 
            $tarjeta->cargarTarjeta($cargasValidasParaPasarse[$i]);
            $output = ob_get_clean(); 

            $expectedOutput = "Carga Denegada. El límite de saldo de una tarjeta es de " . Tarjeta::LIMITESALDO;

            $this->assertEquals($saldoPorPasarse, $tarjeta->saldo);
            $this->assertEquals($output, $expectedOutput);
            
        }

    }

    public function testViajesPlus()
{
    $colectivo = new Colectivo();
    $tarjeta = new Tarjeta();

    $saldosMenoresATarifaBasica = [119, 30, 65, 15, 0];

    for ($i = 0; $i < count($saldosMenoresATarifaBasica); $i++) {
        $viajesPlus = 0;
        $tarjeta->saldo = $saldosMenoresATarifaBasica[$i];
        $boleto = true;

        while ($boleto != false) {
            $boleto = $colectivo->pagarCon($tarjeta);

            if ($boleto != false) {
                $viajesPlus++;
            }
        }

        $this->assertLessThanOrEqual(2, $viajesPlus, "La variable \$viajesPlus no es menor o igual a 2");
    }
}

    }

