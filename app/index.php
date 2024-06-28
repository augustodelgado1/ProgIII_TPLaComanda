<?php

require_once '../vendor/autoload.php';


require_once './Controller/UsuarioController.php';
require_once './Controller/EmpleadoController.php';
require_once './Controller/SectorController.php';
require_once './Controller/MesaController.php';
require_once './Controller/ProductoController.php';
require_once './Controller/TipoDeProductoController.php';
require_once './Controller/OrdenController.php';
require_once './Controller/PedidoController.php';
require_once './Controller/EncuestaController.php';
require_once './Controller/SocioController.php';
require_once './Controller/CargoController.php';
require_once './Controller/PuntuacionController.php';
require_once './Controller/RolController.php';
require_once './Herramientas/File.php';




require_once './middlewares/ValidadorMiddleware.php';
require_once './middlewares/AuthLogMiddleware.php';
require_once './middlewares/ValidarCargo.php';
require_once './middlewares/VerificarRoles.php';
require_once './middlewares/ValidadorGetMiddleware.php';
require_once './middlewares/ValidadorTokenMiddleware.php';

Use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// 4to Sprint ( Entrega 01 de Julio)

// ❖ Hacer todo el circuito de un pedido.
// ❖ Manejo del estado del pedido + estadísticas 30 días
// ❖ Descarga de archivos PDF
// ❖ Seguimiento de las acciones de los empleados.
// ❖ Manejo del estado de los empleado



$app = AppFactory::create();
$app->addBodyParsingMiddleware();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$app->group('/prueva', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',function($request, $response, array $args) 
	{
		$data = $request->getParsedBody();
	
		return $response;
	});

});

$app->group('/usuario', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\UsuarioController::class.':Login')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarLoggin'),"Debe ingresar un email y una clave valida"));

});


$app->group('/empleado', function (RouteCollectorProxy $grupoDeRutas) 
{
	//ABM
	$grupoDeRutas->post('[/]',\EmpleadoController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del empleado"))
	->add(new VerificarRoles(array('Socio')));


	$grupoDeRutas->put('[/]',\EmpleadoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del empleado"))
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarRolEmpleado'),"Debe ingresar los datos completos del empleado"))
	->add(new VerificarRoles(array('Socio')));;;


	$grupoDeRutas->put('/{suspender}',\EmpleadoController::class.':SuspenderUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarRolEmpleado'),"El empleado ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));


	$grupoDeRutas->delete('[/]',\EmpleadoController::class.':EliminarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarRolEmpleado'),"El empleado ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');

	$grupoDeRutas->get('/cargo',\EmpleadoController::class.':ListarPorCargo')
	->add(new ValidadorMiddleware(array(Cargo::class,'VerificarDescripcion'),"El cargo ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->get('/{pedidos}',\EmpleadoController::class.':ListarPedidosPendientes')
	->add(new AuthLogMiddleware('Listar Pedidos Pendientes'))
	->add(new VerificarRoles(array('Empleado')));;
});

//LISTADOS
	
$app->group('/consultaEmpleados', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\EmpleadoController::class.':ListarSuspendidos')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->get('/{borrados}',\EmpleadoController::class.':ListarBorrados')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->post('[/]',\EmpleadoController::class.':ListarCantidadDeTareasRealizadas')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->post('/{sector}',\EmpleadoController::class.':ListarCantidadDeTareasRealizadasSector')
	->add(new VerificarRoles(array('Socio')));;;
});

$app->group('/socio', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\SocioController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del Socio"))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->put('[/]',\SocioController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'Validador'),"Debe ingresar los datos completos del Socio"))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->delete('[/]',\SocioController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Usuario::class,'ValidarRolSocio'),"El Socio ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));
	
	$grupoDeRutas->get('[/]',\SocioController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));
});

$app->group('/producto', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\ProductoController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Producto::class,'Validador'),"Debe ingresar todos los datos del producto (nombre,tipo de producto,precio) "))
	->add(new VerificarRoles(array('Socio')));
	
	$grupoDeRutas->get('[/]',\ProductoController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->get('/tipo',\ProductoController::class.':ListarPorTipoDeProducto')
	->add(new ValidadorMiddleware(array(Producto::class.'ValidarTipo'),"El tipo ingresado no existe"))
	;

	$grupoDeRutas->put('[/]',\ProductoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Producto::class,'Validador'),"Debe ingresar todos los datos del producto (nombre,tipo de producto,precio) "))
	->add(new ValidadorMiddleware(array(Producto::class,'VerificarUno'),"El Producto ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));
	
	$grupoDeRutas->delete('[/]',\ProductoController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Producto::class,'VerificarUno'),"El Producto ingresado no existe"))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->post('/{csv}',\ProductoController::class.':GuardarListaEnCsv')
	->add(new ValidadorMiddleware(array(File::class,'ValidarNombreDelArchivo'),'Debe Ingresar Un Nombre para el archivo'))
	->add(new VerificarRoles(array('Socio')));
	
	$grupoDeRutas->get('/{csv}',\ProductoController::class.':CargarListaPorCsv')
	->add(new ValidadorGetMiddleware(array(File::class,'ValidarExistenciaDelArchivo'),'El archivo no existe'))
	->add(new ValidadorGetMiddleware(array(File::class,'ValidarNombreDelArchivo'),'Debe Ingresar Un Nombre para el archivo'))
	->add(new VerificarRoles(array('Socio')));

	
});

$app->group('/pedido', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\PedidoController::class.':CargarUno')
	->add(new AuthLogMiddleware('Creacion De Pedido'))
	->add(new ValidadorMiddleware(array(Pedido::class,'ValidadorAlta'),'Debe ingresar el nombre del producto y el tipo y el codigo de una orden activa'))
	->add(new ValidarCargo(array('mozo')));

	$grupoDeRutas->put('[/]',\PedidoController::class.':ModificarUno')
	->add(new AuthLogMiddleware('Modificacion De Pedido'))
	->add(new ValidadorMiddleware(array(Orden::class,'ValidadorCodigo'),'el codigo ingresadado no existe'))
	->add(new ValidarCargo(array('mozo')))
	->add(new VerificarRoles(array('Empleado')));
	
	$grupoDeRutas->put('/preparar',\PedidoController::class.':PreapararUnPedido')
	->add(new AuthLogMiddleware('Preparar un Pedido'))
	->add(new ValidadorMiddleware(array(Pedido::class,'ValidadorPreparacion'),'Debe ingresar horas y minutos estimado de preparacion'))
	->add(new ValidadorMiddleware(array(Pedido::class,'VerificarCodigo'),'el codigo ingresadado no existe'))
	->add(new VerificarRoles(array('Empleado')));;


	$grupoDeRutas->put('/{finalizacion}',\PedidoController::class.':FinalizarPreparacionDeUnPedido')
	->add(new AuthLogMiddleware('Finalizar Preparacion De Un Pedido'))
	->add(new ValidadorMiddleware(array(Pedido::class,'VerificarCodigo'),'el codigo ingresadado no existe'))
	->add(new VerificarRoles(array('Empleado')));


	$grupoDeRutas->delete('/{cancelar}',\PedidoController::class.':CancelarUnPedido')
	->add(new AuthLogMiddleware('Cancelacion De Pedido'))
	->add(new ValidadorMiddleware(array(Pedido::class,'VerificarCodigo'),'el codigo ingresadado no existe'))
	->add(new ValidarCargo(array('mozo')))
	->add(new VerificarRoles(array('Empleado')));;

	$grupoDeRutas->delete('[/]',\PedidoController::class.':BorrarUnPedido')
	->add(new AuthLogMiddleware('Baja De Pedido'))
	->add(new ValidadorMiddleware(array(Pedido::class,'VerificarCodigo'),'el codigo ingresadado no existe'))
	->add(new ValidarCargo(array('mozo')))
	->add(new VerificarRoles(array('Empleado')));;

	
	$grupoDeRutas->get('[/]',\PedidoController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->get('/{terminados}',\PedidoController::class.':ListarTerminados')
	->add(new AuthLogMiddleware('Listar Pedidos Terminados'))
	->add(new ValidarCargo(array('mozo')));
});

$app->group('/orden', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\OrdenController::class.':CargarUno')
	->add(new AuthLogMiddleware('Creacion De Orden'))
	->add(new ValidadorMiddleware(array(Orden::class,'Validador'),'Debe ingresar todos los datos de la Orden'))
	->add(new ValidarCargo(array('mozo')));

	$grupoDeRutas->post('/{foto}',\OrdenController::class.':AgregarFoto')
	->add(new AuthLogMiddleware('Agregar Foto a Orden'))
	->add(new ValidadorMiddleware(array(Orden::class,'ValidadorCodigo'),'el codigo ingresadado no existe'))
	->add(new ValidarCargo(array('mozo')));;

	$grupoDeRutas->get('/{obtener}',\OrdenController::class.':ListarUno')
	->add(new ValidadorGetMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'el codigo ingresadado no existe'))
	->add(new ValidadorGetMiddleware(array(Orden::class,'ValidadorCodigo'),'el codigo ingresadado no existe'));
	
	
	$grupoDeRutas->put('[/]',\OrdenController::class.':ModificarUno')
	->add(new AuthLogMiddleware('Modificacion De Orden'))
	->add(new ValidadorMiddleware(array(Orden::class,'ValidadorCodigo'),'el codigo ingresadado no existe'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->delete('[/]',\OrdenController::class.':BorrarUno')
	->add(new AuthLogMiddleware('Baja De Orden'))
	->add(new ValidadorMiddleware(array(Orden::class,'ValidadorCodigo'),'el codigo ingresadado no existe'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->get('[/]',\OrdenController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));
});


$app->group('/encuesta', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->post('[/]',\EncuestaController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorAlta'),'Debe Ingresar un nombre del cliente y '));
	
	$grupoDeRutas->put('[/]',\EncuestaController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorModificacion'),'Debe Ingresar todos los datos de la encuesta'))
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorId'),'la encuesta ingresada no existe '));;
	
	
	$grupoDeRutas->delete('[/]',\EncuestaController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Encuesta::class,'ValidadorId'),'la encuesta ingresada no existe '));;

	$grupoDeRutas->get('[/]',\EncuestaController::class.':Listar');
});


$app->group('/mesa', function (RouteCollectorProxy $grupoDeRutas) 
{
	// $grupoDeRutas->get('[/]',\EmpleadoController::class.':Listar');
	$grupoDeRutas->get('[/]',\MesaController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->post('[/]',\MesaController::class.':CargarUno');
	// $grupoDeRutas->put('[/]',\MesaController::class.':ModificarUno');

	$grupoDeRutas->put('[/]',\MesaController::class.':SetEstadoServirComida')
		->add(new AuthLogMiddleware('Modificacion De Estado De Mesa a Servir Comida'))
		->add(new ValidadorMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'la Mesa ingresada no existe '))
		->add(new ValidarCargo(array('mozo')));

	$grupoDeRutas->put('/{pagar}',\MesaController::class.':SetEstadoPagarOrden')
		->add(new AuthLogMiddleware('Modificacion De Estado De Mesa a Cobrar Cuenta'))
		->add(new ValidadorMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'la Mesa ingresada no existe '))
		->add(new ValidarCargo(array('mozo')));

	$grupoDeRutas->delete('[/]',\MesaController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'la Mesa ingresada no existe '))
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->delete('/{cerrar}',\MesaController::class.':SetEstadoCerrarMesa')
		->add(new ValidadorMiddleware(array(Orden::class,'ValidadorCodigo'),'la Orden ingresada no existe '))
		->add(new ValidadorMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'la Mesa ingresada no existe '))
		->add(new VerificarRoles(array('Socio')));;;

});

$app->group('/consultaMesa',function (RouteCollectorProxy $grupoDeRutas)
{
	$grupoDeRutas->get('[/]',\MesaController::class.':ListarMesaMasUsada')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->get('/{menos}',\MesaController::class.':ListarMesaMenosUsada')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->post('[/]',\MesaController::class.':ListarMesasConMayorImpote')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->post('/{menor}',\MesaController::class.':ListarMesasConMenorImpote')
	->add(new VerificarRoles(array('Socio')));;;
	
});

$app->group('/comentarios',function (RouteCollectorProxy $grupoDeRutas)
{
	$grupoDeRutas->get('[/]',\MesaController::class.':ListarComentariosPositivosDeLasMesas')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->get('/negativos',\MesaController::class.':ListarComentariosNegativosDeLasMesas')
	->add(new VerificarRoles(array('Socio')));;;
});

$app->group('/facturacionMesa',function (RouteCollectorProxy $grupoDeRutas)
{
	$grupoDeRutas->get('[/]',\MesaController::class.':ListarMesaMasFacturo')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->get('/{menos}',\MesaController::class.':ListarMesaMenosFacturo')
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->post('[/]',\MesaController::class.':ListarFacturacionEntreDosFechas')
	->add(new ValidadorMiddleware(array(Mesa::class,'ValidadorCodigoDeMesa'),'la Mesa ingresada no existe '))
	->add(new VerificarRoles(array('Socio')));;;
});

$app->group('/consultaPedidos', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\PedidoController::class.':ListarNoEntregadoEnElTimpoEstipulado')
	->add(new VerificarRoles(array('Socio')));;;;

	$grupoDeRutas->get('/{cancelados}',\PedidoController::class.':ListarCancelados')
	->add(new VerificarRoles(array('Socio')));;;;

	$grupoDeRutas->post('[/]',\PedidoController::class.':ListarElPedidoMasVendido')
	->add(new VerificarRoles(array('Socio')));;;;

	$grupoDeRutas->post('/{menos}',\PedidoController::class.':ListarElPedidoMenosVendido')
	->add(new VerificarRoles(array('Socio')));;;;
});

$app->group('/cargo', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\CargoController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->post('[/]',\CargoController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Cargo::class,'Validador'),'Debe ingresar la descripcion'))
	->add(new VerificarRoles(array('Socio')));;;;

	$grupoDeRutas->put('[/]',\CargoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Cargo::class,'Validador'),'Debe ingresar la descripcion'))
	->add(new VerificarRoles(array('Socio')));;;;

	$grupoDeRutas->delete('[/]',\CargoController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Cargo::class,'VerificarUno'),'Debe ingresar la descripcion'))
	->add(new VerificarRoles(array('Socio')));;;;
});

$app->group('/tipoDeProducto', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\TipoDeProductoController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->post('[/]',\TipoDeProductoController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(TipoDeProducto::class,'Validador'),'Debe ingresar el nombre del tipo'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->put('[/]',\TipoDeProductoController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(TipoDeProducto::class,'Validador'),'Debe ingresar el nombre del tipo'))
	->add(new ValidadorMiddleware(array(TipoDeProducto::class,'VerificarUno'),'El id ingresado no pertenece a ningun tipo de producto'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->delete('[/]',\TipoDeProductoController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(TipoDeProducto::class,'VerificarUno'),'El id ingresado no pertenece a ningun tipo de producto'))
	->add(new VerificarRoles(array('Socio')));;
});

$app->group('/puntuacion', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->post('[/]',\PuntuacionController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Puntuacion::class,'Validador'),'Debe ingresar el nombre y la puntuacion y el id de la encuesta'))
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->put('[/]',\PuntuacionController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Puntuacion::class,'Validador'),'Debe ingresar el nombre y la puntuacion y el id de la encuesta'))
	->add(new ValidadorMiddleware(array(Puntuacion::class,'VerificarUno'),'El id ingresado no pertenece a ninguna puntuacion'))
	->add(new VerificarRoles(array('Socio')));;;

	$grupoDeRutas->delete('[/]',\PuntuacionController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Puntuacion::class,'VerificarUno'),'El id ingresado no pertenece a ninguna puntuacion'))
	->add(new VerificarRoles(array('Socio')));
});

$app->group('/sector', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\SectorController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->post('[/]',\SectorController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Sector::class,'Validador'),'Debe ingresar el nombre del Sector'))
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->put('[/]',\SectorController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Sector::class,'Validador'),'Debe ingresar el nombre del Sector'))
	->add(new ValidadorMiddleware(array(Sector::class,'VerificarUno'),'El id ingresado no pertenece a ninguna puntuacion'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->delete('[/]',\SectorController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Sector::class,'VerificarUno'),'El id ingresado no pertenece a ninguna puntuacion'))
	->add(new VerificarRoles(array('Socio')));;
});

$app->group('/rol', function (RouteCollectorProxy $grupoDeRutas) 
{
	$grupoDeRutas->get('[/]',\RolController::class.':Listar')
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->post('[/]',\RolController::class.':CargarUno')
	->add(new ValidadorMiddleware(array(Rol::class,'Validador'),'Debe ingresar el nombre del Rol'))
	->add(new VerificarRoles(array('Socio')));;

	$grupoDeRutas->put('[/]',\RolController::class.':ModificarUno')
	->add(new ValidadorMiddleware(array(Rol::class,'Validador'),'Debe ingresar el nombre del Rol'))
	->add(new ValidadorMiddleware(array(Rol::class,'VerificarUno'),'El id ingresado no pertenece a ningun Rol'))
	->add(new VerificarRoles(array('Socio')));

	$grupoDeRutas->delete('[/]',\RolController::class.':BorrarUno')
	->add(new ValidadorMiddleware(array(Rol::class,'VerificarUno'),'El id ingresado no pertenece a ningun Rol'))
	->add(new VerificarRoles(array('Socio')));;
});

$app->run();
?>