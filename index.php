<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './middlewares/AuthMW.php';
require_once './middlewares/AuthMozoMW.php';
require_once './middlewares/AuthSocioMW.php';
require_once './middlewares/EstadoYTiempoProductoMW.php';
require_once './utils/AutentificadorJWT.php';


// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//Routes
// $app->get('[/]', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('[/]', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/test', function (Request $request, Response $response) {    
//     $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });



$app->group('/usuarios', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(new AuthSocioMW());
    $group->get('/CargarUsuarios', \UsuarioController::class . ':CargarUsuariosEnCSV')->add(new AuthSocioMW());
    $group->get('/DescargarUsuarios', \UsuarioController::class . ':DescargarUsuariosDesdeCSV')->add(new AuthSocioMW());
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new AuthSocioMW());
    $group->put('[/]', \UsuarioController::class . ':ModificarUno')->add(new AuthSocioMW());
    $group->delete('[/{idUsuario}]', \UsuarioController::class . ':BorrarUno')->add(new AuthSocioMW());
})->add(new AuthMW());

$app->group('/mesas', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(new AuthMozoMW());
    $group->get('/MasUsada', \MesaController::class . ':TraerMesaMasUsada')->add(new AuthSocioMW());
    $group->get('/MejoresComentarios', \MesaController::class . ':TraerMejoresComentarios')->add(new AuthSocioMW());
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new AuthSocioMW());
    $group->put('[/]', \MesaController::class . ':ModificarUno')->add(new AuthMozoMW());
    $group->put('/CerrarMesa', \MesaController::class . ':SocioCierraMesa')->add(new AuthSocioMW());
    $group->delete('[/{idMesa}]', \MesaController::class . ':BorrarUno')->add(new AuthSocioMW());
})->add(new AuthMW());

$app->group('/productos', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(new AuthMozoMW());
    $group->get('/TraerTodosSegunEstado', \ProductoController::class . ':TraerProductosSegunEstado');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new AuthMozoMW());
    $group->put('[/]', \ProductoController::class . ':ModificarUno')->add(new AuthMozoMW());
    $group->put('/EmpleadoTomaProducto', \ProductoController::class . ':EmpleadoTomaProducto')->add(new EstadoYTiempoProductoMW());
    $group->put('/EmpleadoAlistaProducto', \ProductoController::class . ':EmpleadoAlistaProducto')->add(new EstadoYTiempoProductoMW());
    $group->delete('[/{idProducto}]', \ProductoController::class . ':BorrarUno')->add(new AuthSocioMW());
})->add(new AuthMW());

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/TraerTodosSegunEstado', \PedidoController::class . ':TraerPedidosSegunEstado');
    $group->post('[/]', \PedidoController::class . ':CargarUno');
    $group->put('[/]', \PedidoController::class . ':ModificarUno');
    $group->put('/MozoPedidoCliente', \PedidoController::class . ':MozoPedidoCliente');
    $group->delete('[/{idPedido}]', \PedidoController::class . ':BorrarUno');
})->add(new AuthMW())->add(new AuthMozoMW());

$app->group('/cliente', function (RouteCollectorProxy $group)
{
    $group->get('[/]', \PedidoController::class . ':TraerPedidoCliente');
    $group->put('[/]', \PedidoController::class . ':ClienteCalificaPedido');
})->add(new AuthMW());

$app->group('/login', function (RouteCollectorProxy $group)
{
    $group->post('[/]', \UsuarioController::class . ':LoginUsuario');
});

$app->run();
