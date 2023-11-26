<?php

require_once './models/cuenta.php';

class cuentaController extends cuenta
{
    public function CargarUno($request, $response, $args)
    {

        $params = $request->getParsedBody();

        $nombreYApellido = $params['nombreYApellido'];
        $tipoDeDocumento = $params['tipoDeDocumento'];
        $nroDocumento = $params['nroDocumento'];
        $mail = $params['mail'];
        $tipoDeCuenta = $params['tipoDeCuenta'];
        $moneda = $params['moneda'];

        $nroDeCuenta = Cuenta::ObtenerNroDeCuenta($nroDocumento);

        if(isset($params['saldoInicial']))
        {
            $saldoInicial = $params['saldoInicial'];
        }
        else
        {
            $saldoInicial = 0;
        }
    

        if(!(Cuenta::ExisteCuentaPorNroDeCuenta($nroDeCuenta)))
        {
            $nuevaCuenta = new Cuenta();
            $nuevaCuenta->nombreYApellido = $nombreYApellido;
            $nuevaCuenta->tipoDeDocumento = $tipoDeDocumento;
            $nuevaCuenta->nroDocumento = $nroDocumento;
            $nuevaCuenta->mail = $mail;
            $nuevaCuenta->tipoDeCuenta = $tipoDeCuenta . $moneda;
            $nuevaCuenta->moneda = $moneda;
            $nuevaCuenta->estado = "Activo";
            $nuevaCuenta->urlImagen = Cuenta::GuardarImagenCuenta("ImagenesDeCuentas/2023/", $_FILES['urlImagen'], $tipoDeCuenta, $nroDeCuenta); 

            $nuevaCuenta->CrearCuenta();

            $payload = json_encode(array("mensaje" => "La cuenta se ha creado exitosamente"));
        }
        else
        {
            $saldo = Cuenta::ObtenerSaldoCuenta($nroDeCuenta);
            $saldo = $saldo + $saldoInicial;
            if(Cuenta::ModificarSaldoCuenta($nroDeCuenta, $tipoDeCuenta, $saldo) > 0)
            {
                $payload = json_encode(array("mensaje" => "La cuenta ya existe, se le sumo el nuevo saldo al saldo anterior"));
            }
            else
            {
                $payload = json_encode(array("mensaje" => "La cuenta ya existe, pero el tipo de cuenta no es el mismo, no se le pudo sumar el saldo"));
            }
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Cuenta::ObtenerTodos();
        $payload = json_encode(array("listaDeCuentas" => $lista));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $nroDeCuenta = $args["nroDeCuenta"];

        $cuenta = Cuenta::ObtenerCuenta($nroDeCuenta);
        $payload = json_encode($cuenta);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $nombreYApellido = $params['nombreYApellido'];
        $tipoDeDocumento = $params['tipoDeDocumento'];
        $nroDocumento = $params['nroDocumento'];
        $mail = $params['mail'];
        $tipoDeCuenta = $params['tipoDeCuenta'];
        $moneda = $params['moneda'];
        $nroDeCuenta = $params['nroDeCuenta'];

        if((Cuenta::ExisteCuentaPorNroDeCuenta($nroDeCuenta)))
        {
            if(Cuenta::ModificarCuenta($nombreYApellido, $tipoDeDocumento, $nroDocumento, $mail, $tipoDeCuenta . $moneda, $moneda, $nroDeCuenta) > 0)
            {
                $payload = json_encode(array("mensaje" => "La cuenta {$nroDeCuenta} se actualizo correctamente"));
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se realizaron modificaciones"));
            }
        }
        
        else
        {
            $payload = json_encode(array("mensaje" => "No existe cuenta con ese nroDeCuenta"));
        }
           

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
        
    }
    
    public function BorrarUno($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $nroDeCuenta = $params['nroDeCuenta'];
        $tipoDeCuenta = $params['tipoDeCuenta'];

        if(Cuenta::BorrarCuenta($nroDeCuenta, $tipoDeCuenta, "Inactivo") > 0)
        {          
            $urlImagen = Cuenta::ObtenerImagen($nroDeCuenta);
            $nuevoDestino = "ImagenesBackupCuentas/2023/" . $nroDeCuenta . $tipoDeCuenta . ".jpg";
            if(rename($urlImagen, $nuevoDestino))
            {
                $payload = json_encode(array("mensaje" => "La cuenta {$nroDeCuenta} se dio de baja y se guardo la foto en el backup"));
            }
            else
            {
                $payload = json_encode(array("mensaje" => "La cuenta {$nroDeCuenta} se dio de baja pero ocurrio un error al guardar la foto"));
            }
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Ocurrio un error al hacer la baja"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarCuentas($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $nroDeCuenta = $params['nroDeCuenta'];
        $tipoDeCuenta = $params['tipoDeCuenta'];

        $mensaje = Cuenta::ConsultarCuentaPorNroYTipo($nroDeCuenta, $tipoDeCuenta);

        $payload = json_encode(array("mensaje" => "{$mensaje}"));

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


}

?>