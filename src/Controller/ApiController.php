<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\RutaFavorita;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends FOSRestController {
    // USER URI's

    /**
     * @Rest\Post("/login_check", name="user_login_check")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Éxito, el usuario ha sido logeado"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error, el usuario no se ha podido logear"
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="El email del usuario",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="La contraseña del usuario",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Usuarios")
     */
    public function getLoginCheckAction() {}

    /**
     * @Rest\Post("/register", name="user_register")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Éxito, el usuario ha sido registrado"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error, el usuario no ha podido ser registrado"
     * )
     *
     * @SWG\Parameter(
     *     name="_nombre",
     *     in="body",
     *     type="string",
     *     description="El nombre del usuario",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="_telefono",
     *     in="body",
     *     type="string",
     *     description="El teléfono del usuario",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="El email del usuario",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="query",
     *     type="string",
     *     description="La contraseña del usuario"
     * )
     * 
     * @SWG\Parameter(
     *     name="_token_dispositivo",
     *     in="query",
     *     type="string",
     *     description="El id del dispositivo móvil del usuario",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="Usuarios")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        $user = [];
        $message = "";

        try {
            $code = 200;
            $error = false;

            $nombre = $request->request->get('_nombre');
            $telefono = $request->request->get('_telefono');
            $email = $request->request->get('_email');
            $password = $request->request->get('_password');
            $token_dispositivo = $request->request->get('_token_dispositivo');

            $user = new Usuarios();
            $user->setNombre($nombre);
            $user->setTelefono($telefono);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setTokenDispositivo($token_dispositivo);

            $em->persist($user);
            $em->flush();
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Error, el usuario no ha podido ser registrado - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    // RUTA URI's

    /**
     * @Rest\Get("/v1/rutas_favoritas.{_format}", name="rutas_favoritas_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Obtiene todas las rutas favoritas para el actual usuario logeado."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error, no se han podido obtener las rutas favoritas."
     * )
     *
     * @SWG\Tag(name="Rutas favoritas")
     */
    public function getAllRutasFavoritasAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $rutas_favoritas = [];
        $message = "";

        try {
            $code = 200;
            $error = false;

            $id_usuario = $this->getUser()->getId();
            $rutas_favoritas = $em->getRepository("App:RutaFavorita")->findBy([
                "id_usuario" => $id_usuario,
            ]);

            if (is_null($rutas_favoritas)) {
                $rutas_favoritas = [];
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Error, no se han podido obtener las rutas favoritas - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $rutas_favoritas : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    
    /**
     * @Rest\Post("/v1/rutas_favoritas.{_format}", name="rutas_favoritas_add", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Éxito, la ruta ha sido añadida a favoritos."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error, no se ha podido añadir la ruta a favoritos."
     * )
     *
     * @SWG\Parameter(
     *     name="idlinea",
     *     in="body",
     *     type="string",
     *     description="El id de la línea",
     *     schema={}
     * )
     *  
     * @SWG\Parameter(
     *     name="codigo",
     *     in="body",
     *     type="string",
     *     description="La código de la línea",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="dias",
     *     in="body",
     *     type="string",
     *     description="La frecuencia de paso de la línea",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="hora_salida",
     *     in="body",
     *     type="string",
     *     description="La hora de salida",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="hora_llegada",
     *     in="body",
     *     type="string",
     *     description="La hora de llegada",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="operadores",
     *     in="body",
     *     type="string",
     *     description="Las distintas empresas que ofrecen servicio para esta línea",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="precio_billete_sencillo",
     *     in="body",
     *     type="decimal",
     *     description="Precio del billete sencillo",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="precio_tarjeta",
     *     in="body",
     *     type="decimal",
     *     description="Precio con tarjeta del consorcio",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="tiempo_estimado",
     *     in="body",
     *     type="decimal",
     *     description="Duración estiamda del viaje",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="pmr",
     *     in="body",
     *     type="bool",
     *     description="Adaptado o no a personas con movilidad reducida",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="linea",
     *     in="body",
     *     type="object",
     *     description="Línea a la que pertenece la ruta",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="origen",
     *     in="body",
     *     type="object",
     *     description="Origen de la ruta",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="destino",
     *     in="body",
     *     type="object",
     *     description="Destino de la ruta",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="_id",
     *     in="body",
     *     type="string",
     *     description="El id del usuario",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="Rutas favoritas")
     */
    public function addRutaFavoritaAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ruta_favorita = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $idlinea = $request->request->get("idlinea", null);
            $dias = $request->request->get("dias", null);
            $codigo = $request->request->get("codigo", null);
            $horaSalida = $request->request->get("hora_salida", null);
            $horaLlegada = $request->request->get("hora_llegada", null);
            $operadores = $request->request->get("operadores", null);
            $precio_billete_sencillo = $request->request->get("precio_billete_sencillo", null);
            $precio_tarjeta = $request->request->get("precio_tarjeta", null);
            $tiempo_estimado = $request->request->get("tiempo_estimado", null);
            $pmr = $request->request->get("pmr", null);
            $linea = $request->request->get("linea", null);
            $origen = $request->request->get("origen", null);
            $destino = $request->request->get("destino", null);
            $id_usuario = $this->getUser()->getId();
            $user = $this->getDoctrine()
                ->getRepository(Usuarios::class)
                ->findOneBy([
                    'id' => $id_usuario,
                ]);

            if (!is_null($idlinea) && !is_null($dias) && !is_null($codigo) && !is_null($horaSalida) && !is_null($horaLlegada) && !is_null($operadores) && !is_null($precio_billete_sencillo) && !is_null($pmr) && !is_null($linea) && !is_null($origen) && !is_null($destino)) {
                $ruta_favorita = new RutaFavorita();
                $ruta_favorita->setIdLinea($idlinea);
                $ruta_favorita->setDias($dias);
                $ruta_favorita->setCodigo($codigo);
                $ruta_favorita->setHoraSalida($horaSalida);
                $ruta_favorita->setHoraLlegada($horaLlegada);
                $ruta_favorita->setOperadores($operadores);
                $ruta_favorita->setPrecio_billete_sencillo($precio_billete_sencillo);
                $ruta_favorita->setPrecioTarjeta($precio_tarjeta);
                $ruta_favorita->setTiempoEstimado($tiempo_estimado);
                $ruta_favorita->setPmr($pmr);
                $ruta_favorita->setLinea(($linea));
                $ruta_favorita->setOrigen($origen);
                $ruta_favorita->setDestino($destino);
                $ruta_favorita->setIdUsuario($user);

                $em->persist($ruta_favorita);
                $em->flush();
            } else {
                $code = 500;
                $error = true;
                $message = "Error, no se ha podido añadir la ruta a favoritos - Error: No se han enviado todos los parámetros necesarios";
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Error, no se ha podido añadir la ruta a favoritos - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            // 'token' => $token_dispositivo,
            'data' => $code == 201 ? $ruta_favorita : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/v1/rutas_favoritas/{id}.{_format}", name="rutas_favoritas_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Éxito, la ruta ha sido eliminada de favoritos"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error, no se ha podido eliminar la ruta de favoritos"
     * )
     * 
     * @SWG\Parameter(
     *     name="_id",
     *     in="body",
     *     type="string",
     *     description="El id del usuario",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="Rutas favoritas")
     */
    public function deleteRutaFavoritaAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ruta_favorita = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $ruta_favorita = $this->getDoctrine()->getRepository(RutaFavorita::class)->findOneBy(['id'=>$id]);

            if (!is_null($ruta_favorita)) {
                $em->remove($ruta_favorita);
                $em->flush();
            } else {
                $code = 500;
                $error = true;
                $message = "Error, no se ha podido eliminar la ruta de favoritos - Error: Debes proporcionar un id de ruta";
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Error, no se ha podido eliminar la ruta de favoritos - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 201 ? $ruta_favorita : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

}
