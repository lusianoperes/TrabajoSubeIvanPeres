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

    public function testDescontarViajePlus()
    {
        $colectivo = new Colectivo();
        $tarjeta = new Tarjeta();

        $cargasPermitidas = Tarjeta::VALORESDECARGAPERMITIDOS;
        $deudas = [211.84, 100, 3];
    
        for ($i = 0; $i < count($deudas); $i++)
        {
            $tarjeta->saldo = 0;
            $tarjeta->deuda = $deudas[$i];

            for ($j = 0; $j < count($cargasPermitidas); $j++)
            {
                
                $tarjeta->cargarTarjeta($cargasPermitidas[$j]);

                $deudaAux = $tarjeta->deuda;
                $saldoPrePago = $tarjeta->saldo;
                $retorno = $colectivo->pagarCon($tarjeta);

                if($deudaAux <= $saldoPrePago && ($saldoPrePago - $deudaAux) >= Colectivo::TARIFABÁSICA)
                {

                    $this->assertEquals($tarjeta->deuda,  0);
                    $this->assertEquals($tarjeta->saldo,  $saldoPrePago - $deudaAux - $retorno->obtenerCostoViaje());

                }
                else
                {

                    $this->assertEquals(false, $retorno);

                }
                
                $tarjeta->saldo = 0;
                $tarjeta->deuda = $deudas[$i];

            }

        }

    }

    public function testPagarConFranquiciaCompleta()
    {
        $colectivo = new Colectivo();
        $tarjeta = new TarjetaJubilado();

        $saldosParaPagar = [-200.12, 0, 150, 4400, 6600];

        for($i = 0; $i < count($saldosParaPagar); $i++)
        {

            $tarjeta->saldo = $saldosParaPagar[$i];
            $saldoPrePago = $tarjeta->saldo;

            $retorno = $colectivo->pagarCon($tarjeta);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals(0, $retorno->costoViaje);

            $this->assertEquals($tarjeta->saldo, $saldoPrePago);

            $this->assertEquals($tarjeta->saldo, $retorno->saldoRestante);

        }
         
    }

    public function testPagarConMedioBoleto()
    {
        $colectivo = new Colectivo();
        $tarjeta = new TarjetaEstudiantil();

        $saldosParaPagar = [10, 0, 150, 4400, 6600];

        for($i = 0; $i < count($saldosParaPagar); $i++)
        {

            $tarjeta->saldo = $saldosParaPagar[$i];
            $saldoPrePago = $tarjeta->saldo;

            $retorno = $colectivo->pagarCon($tarjeta);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals(Colectivo::TARIFABÁSICA / 2, $retorno->costoViaje);

            $this->assertEquals($tarjeta->saldo, $retorno->saldoRestante);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA / 2, $tarjeta->saldo);

        }
         
    }

    public function testTiposDeBoletosSegunTarjeta()
    {
        $colectivo = new Colectivo(5); 

        $tarjetas = [new Tarjeta(69, 1200, 30), new TarjetaEstudiantil(3, 4000, 500), new TarjetaUniversitaria(1000, 100, 0), new TarjetaJubilado(999, 6000, 200)];

        for($i = 0; $i < count($tarjetas); $i++)
        {
            $saldoPrePago = $tarjetas[$i]->saldo;
            $retorno = $colectivo->pagarCon($tarjetas[$i]);
            $fechaAux = date('Y-m-d');

            $this->assertInstanceOf(Boleto::class, $retorno);
            
            $this->assertEquals($fechaAux, $retorno->obtenerFecha());

            $this->assertEquals($tarjetas[$i]->ID, $retorno->obtenerID());

            $this->assertEquals($tarjetas[$i]->tipoDeTarjeta, $retorno->obtenerTipoDeTarjeta());

            $this->assertEquals($colectivo->lineaDeColectivo, $retorno->obtenerLinea());
            
            if($tarjetas[$i]->tipoDeTarjeta == "Estudiantil" || $tarjetas[$i]->tipoDeTarjeta == "Universitaria")
            {

                $this->assertEquals(Colectivo::TARIFABÁSICA / 2, $retorno->obtenerCostoViaje());
                $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA / 2 - $tarjetas[$i]->deuda, $retorno->obtenerSaldoRestante());

            }
            else if ($tarjetas[$i]->tipoDeTarjeta == "Jubilado")
            {

                $this->assertEquals(0, $retorno->obtenerCostoViaje());
                $this->assertEquals($saldoPrePago - 0 - $tarjetas[$i]->deuda, $retorno->obtenerSaldoRestante());
            
            }
            else
            {

                $this->assertEquals(Colectivo::TARIFABÁSICA, $retorno->obtenerCostoViaje());
                $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA - 30, $retorno->obtenerSaldoRestante());

            }

        }

    }

    public function testCuatroYCincoViajes()
    {
       
        $colectivo = new Colectivo();
        $tarjetas = [new TarjetaEstudiantil(), new TarjetaUniversitaria()];
         
          for($j = 0; $j < count($tarjetas); $j++)
          {

            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 0;
            $viajesaux = $tarjetas[$j]->viajes;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);
            
            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA / 2, $tarjetas[$j]->saldo);

            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 3;
            $viajesaux = $tarjetas[$j]->viajes;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA / 2, $tarjetas[$j]->saldo);

            
            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 5;
            $viajesaux = $tarjetas[$j]->viajes;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA, $tarjetas[$j]->saldo);


            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 5;
            $viajesaux = $tarjetas[$j]->viajes;
            $tarjetas[$j]->ultimo = strtotime(date("H:i")) - 86400;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, 1);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA / 2 , $tarjetas[$j]->saldo);


          }  

    }

    public function testDosViajesBEG()
    {
       
        $colectivo = new Colectivo();
        $tarjetas = [new TarjetaEducativa()];
         
          for($j = 0; $j < count($tarjetas); $j++)
          {

            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 0;
            $viajesaux = $tarjetas[$j]->viajes;
            $tarjetas[$j]->ultimo = null;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);
            
            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago, $tarjetas[$j]->saldo);

            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 1;
            $viajesaux = $tarjetas[$j]->viajes;
            $tarjetas[$j]->ultimo = null;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago, $tarjetas[$j]->saldo);

            
            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 2;
            $viajesaux = $tarjetas[$j]->viajes;
            $tarjetas[$j]->ultimo = null;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, $viajesaux + 1);

            $this->assertEquals($saldoPrePago - Colectivo::TARIFABÁSICA, $tarjetas[$j]->saldo);


            $tarjetas[$j]->saldo = 120;
            $saldoPrePago = $tarjetas[$j]->saldo;

            $tarjetas[$j]->viajes = 2;
            $viajesaux = $tarjetas[$j]->viajes;
            $tarjetas[$j]->ultimo = strtotime(date("H:i")) - 86400;
            $retorno = $colectivo->pagarCon($tarjetas[$j]);

            $this->assertInstanceOf(Boleto::class, $retorno);

            $this->assertEquals($tarjetas[$j]->viajes, 1);

            $this->assertEquals($saldoPrePago, $tarjetas[$j]->saldo);


          }  

    }

    public function testExcesoDeSaldo()
    {
        $colectivo = new Colectivo();
        $tarjeta = new Tarjeta();

        $cargasPermitidas = Tarjeta::VALORESDECARGAPERMITIDOS;
        $saldos = [6000, 4500, 6500, 6600];
    
        for ($i = 0; $i < count($saldos); $i++)
        {
            $tarjeta->saldo = $saldos[$i];

            for ($j = 0; $j < count($cargasPermitidas); $j++)
            {
                
                $saldoprecarga = $tarjeta->saldo;
                $tarjeta->cargarTarjeta($cargasPermitidas[$j]);
                echo "hola es " . $tarjeta->saldo;
                if(($saldoprecarga + $cargasPermitidas[$j]) > Tarjeta::LIMITESALDO)
                {
                    echo " es mayor y saldo es " . $tarjeta->saldo . "y carga es " . $cargasPermitidas[$j];
                    $this->assertEquals($tarjeta->saldo,  6600);
                    $this->assertEquals($tarjeta->exceso,  $cargasPermitidas[$j] - (Tarjeta::LIMITESALDO - $saldoprecarga));

                }
                else
                {
                    echo " no es mayor y saldo es " . $tarjeta->saldo;
                    $this->assertEquals($tarjeta->saldo, $saldoprecarga + $cargasPermitidas[$j]);

                }
                
                $tarjeta->saldo = $saldos[$i];
                $tarjeta->exceso = 0;

            }

        }

    }

    }

