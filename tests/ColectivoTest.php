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





        
    }

}