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
     *     description="User was logged in successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not logged in successfully"
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
     *     description="La contraseña",
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
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
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
     *     description="El telefono",
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
     *     description="The password"
     * )
     * 
     * @SWG\Parameter(
     *     name="_token_dispositivo",
     *     in="query",
     *     type="string",
     *     description="El id del dispositivo",
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
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Get("/v1/usuarios.{_format}", name="usuarios_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Obtiene todos los usuarios de la base de datos."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all usuarios."
     * )
     *
     * @SWG\Tag(name="Usuarios")
     */
    public function getAllUsuariosAction() {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $usuarios = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $usuarios = $em->getRepository("App:Usuarios")->findAll();

            if (is_null($usuarios)) {
                $usuarios = [];
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Usuarios - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $usuarios : $message,
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
     *     description="An error has occurred trying to get all rutas favoritas."
     * )
     *
     * @SWG\Tag(name="RutaFavorita")
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
            $message = "An error has occurred trying to get all rutas favoritas - Error: {$ex->getMessage()}";
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
     *     description="Ruta favorita was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add new ruta favorita"
     * )
     *
     * @SWG\Parameter(
     *     name="idlinea",
     *     in="body",
     *     type="string",
     *     description="El dia de la cita",
     *     schema={}
     * )
     *  
     * @SWG\Parameter(
     *     name="codigo",
     *     in="body",
     *     type="string",
     *     description="La hora de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="dias",
     *     in="body",
     *     type="string",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="horaSalida",
     *     in="body",
     *     type="string",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="horaLlegada",
     *     in="body",
     *     type="string",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="operadores",
     *     in="body",
     *     type="string",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="precio_billete_sencillo",
     *     in="body",
     *     type="decimal",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="pmr",
     *     in="body",
     *     type="bool",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="linea",
     *     in="body",
     *     type="object",
     *     description="Observaciones de la cita",
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
     * @SWG\Tag(name="RutaFavorita")
     */
    public function addCitaAction(Request $request) {
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
            $horaSalida = $request->request->get("horaSalida", null);
            $horaLlegada = $request->request->get("horaLlegada", null);
            $operadores = $request->request->get("operadores", null);
            $precio_billete_sencillo = $request->request->get("precio_billete_sencillo", null);
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
                $ruta_favorita->setPmr($pmr);
                $ruta_favorita->setLinea(($linea));
                $ruta_favorita->setOrigen($origen);
                $ruta_favorita->setDestino($destino);
                $ruta_favorita->setIdUsuario($user);

                // $token_dispositivo = 'cXmtEchpxeU:APA91bEj6vmvW6dtGoVjFYp5fiIs9UF3pXucv-jNMv1nB-EWxZvPHqQgu_91mMKVMsGHrP6q36zxdiWwn3rwiEKV76FYNGywuPaYvAJhwKNps193-8-vE9dxuroiElS8uhPZGzrNCBye';
                // cXmtEchpxeU:APA91bEj6vmvW6dtGoVjFYp5fiIs9UF3pXucv-jNMv1nB-EWxZvPHqQgu_91mMKVMsGHrP6q36zxdiWwn3rwiEKV76FYNGywuPaYvAJhwKNps193-8-vE9dxuroiElS8uhPZGzrNCB
                
                // $token_dispositivo = $user->getTokenDispositivo();
                // $mensaje = 'Nueva cita el dia ' . $dia . ' a las ' . $hora;

                // $this->enviarNotificacionFirebase($token_dispositivo, 'Cita', $mensaje);
                $em->persist($ruta_favorita);
                $em->flush();
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add new ruta favorita - Error: You must to provide a ruta favorita name";
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new ruta favorita - Error: {$ex->getMessage()}";
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
     * @Rest\Put("/v1/citas/{id}.{_format}", name="citas_update", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Cita was updated successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to update cita"
     * )
     *
     * @SWG\Parameter(
     *     name="dia",
     *     in="body",
     *     type="string",
     *     description="El dia de la cita",
     *     schema={}
     * )
     *  
     * @SWG\Parameter(
     *     name="hora",
     *     in="body",
     *     type="string",
     *     description="La hora de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="observaciones",
     *     in="body",
     *     type="string",
     *     description="Observaciones de la cita",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="body",
     *     type="string",
     *     description="El id del usuario",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="Cita")
     */
    public function updateCitaAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $cita = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $cita = $this->getDoctrine()->getRepository(Cita::class)->findOneBy(['id'=>$id]);
            $dia = $request->request->get("dia", null);
            $hora = $request->request->get("hora", null);
            $observaciones = $request->request->get("observaciones", null);

            if (!is_null($dia) && !is_null($hora) && !is_null($observaciones)) {
                $cita->setDia($dia);
                $cita->setHora($hora);
                $cita->setObservaciones($observaciones);

                // $token_dispositivo = 'cXmtEchpxeU:APA91bEj6vmvW6dtGoVjFYp5fiIs9UF3pXucv-jNMv1nB-EWxZvPHqQgu_91mMKVMsGHrP6q36zxdiWwn3rwiEKV76FYNGywuPaYvAJhwKNps193-8-vE9dxuroiElS8uhPZGzrNCBye';
                // cXmtEchpxeU:APA91bEj6vmvW6dtGoVjFYp5fiIs9UF3pXucv-jNMv1nB-EWxZvPHqQgu_91mMKVMsGHrP6q36zxdiWwn3rwiEKV76FYNGywuPaYvAJhwKNps193-8-vE9dxuroiElS8uhPZGzrNCB
                
                // $token_dispositivo = $user->getTokenDispositivo();
                // $mensaje = 'Nueva cita el dia ' . $dia . ' a las ' . $hora;

                // $this->enviarNotificacionFirebase($token_dispositivo, 'Cita', $mensaje);
                $em->persist($cita);
                $em->flush();
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to update cita - Error: You must to provide a cita name";
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to update cita - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            // 'token' => $token_dispositivo,
            'data' => $code == 201 ? $cita : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    public function enviarNotificacionFirebase($token_dispositivo, $titulo, $mensaje) {
        // $url = 'https://fcm.googleapis.com/v1/projects/appreservacitas-4944e/messages:send';

        // $fields = array (
        //         'registration_ids' => array (
        //                 $token_dispositivo
        //         ),
        //         'data' => array (
        //                 "message" => $mensaje
        //         )
        // );
        // $fields = json_encode ( $fields );

        // $headers = array (
        //         'Authorization: Bearer ' . 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDQF4yk0nKNKr0P\ng7Cn2eRQNrBHskKTlfUl2swV3eDxZUyxNqawxeETMZ5VIfskkw26eXnXoc5aSZLs\nQlMh206f1T+rNNQTaihUrKENxWtPvNn1BrmiBm3OaXNNmZWTIqdEtRU+P6n/ODEM\nk0elDa50RgO/bzOGxv4JKsvJFZvNkZgz9jDvwrobKqWv2XFLeJ8JLU9B3t3axY/f\n5EidYaTWKC4k25C1/Y6taKajHq2nMxxFYQqBJTFtBZZitbrjyt3FgBuUm/1Ng3mL\nF70cGlS0lIZ/X5vb8DbycmLn5rXzFpY5hd/yoD8HZxD2fErxUZf7jdg3N4RaQtQV\nwEZtlTDLAgMBAAECggEAPUa5/S0VW1l6+88RHZRNpYs9JJerAC+31TJVR5kjLKJi\nT4ri0gorCY5ia/pKLI57C+7KgMNecvrYX2b2ePFN+UX/7ifrzV3Ey45cDKSWQZBA\ndGVLE5mmCxLFR9QSlhWtwM88Fq0Dn6qJ0wSpo8JgHVAiuWQC/cyqMMPf53JMA1+0\nzJPSv4fZAcrC8l0EJHjc/8HeT+68CK0bibpqx5cM0uAoGFveW8FA16nwsrlRdww7\nWsfusLdWDuyTXvMXi8aYYnyAPQQNI4frO5BA61HlYGCFciX+a0ZH7exTpPkUbwVc\nzxFU6ZUtDcWzs3LbFLfiJnfGMCLvsNnU13ViMPrl6QKBgQDo6x9ZuTI5OrZQIVhg\nCKFW4SeCMvt/54jnKMQNp+S5dIpGccpkjUreLjVYiZ7YeAFBTmbX6WyOfTWzco0w\nnVxcwA0CUVpFn3wtJ887tC3Fv1fHb5xNWnK/GGDlfC98hko1KTob/EqBvgpd+i9p\nYKLczzeR2nnuV2JEf/IRByHQCQKBgQDktpuX91qovmRrWZAcNIH62D2KcUvHSMa2\n8BnStP6TUGl5dUqJCnOhs1esdcJXpL4Or5fSnBkyixEuT6RwGr9Jk/XD7UzsszU0\nviFz5qLu+1BrzL9dsOXG6QIdVGmLSKBrlzBQMM6eEUKFPq+HKUseBXo1W3xACP+y\n1XS5xQSHMwKBgQCnEeg18pps5IlbZt++WtJnwC8XvDwcXdccgGhIp1JwGIEK0Jpt\nj8/RGPIY5PY0rbewwW0RmJTOjE+VvSg7Y9SZjwSF0hbfc+uddD24xKBEhOCQ5KUH\n80X1fqYaVf614pmeEkllQ42qDMfg6xFRFAkeO+DPVRg69yE8o03eGvCNCQKBgQDF\nFb56c4JCCt7JysHuLCkdmZ1eYUblkYb7OWdnNORy2UYqjbIO6Vy/KKYSTN+NWr8U\nNKflqvHjpgbGG4cdu06+/qs3jJNPHRZqPrThBQu+V/3zBtqYx8kZYMybrZPNnGXw\nkLXnREen2kOyQlRLan/6fMnUlOM64wZEesA9HNNMFQKBgQCXkTBny/suV/av8sG8\nfPLLaeeq/lgPpaWkEg82mHR/CNO5rdaP5YYaYeVaOZzl6v2ikd0r2+YemU+XP+iu\nF/PrR78oOiybIQTZaEw/a8RXeWpKRo4yQPNZmG7ndrYqMd5FJu1mY+EUw5n8zKoR\nNugBdMoevqtrG6S1PgkRK/LlLA==',
        //         'Content-Type: application/json'
        // );

        // $ch = curl_init ();
        // curl_setopt ( $ch, CURLOPT_URL, $url );
        // curl_setopt ( $ch, CURLOPT_POST, true );
        // curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        // curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        // curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        // $result = curl_exec ( $ch );
        // echo $result;
        // curl_close ( $ch );




        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'registration_ids' => array(
                $token_dispositivo
            ),
            'notification' => array( 'title' => $titulo, 'body' => $mensaje)
            // 'data' => array( 'title' => $notificationTitle, 'body' => $notificationBody)
        );
        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . 'AAAAgb3BG3U:APA91bF5LjLLQLO3LAEm4zkGfV9CdbTmnsA4AIRUbYwZwGI5zSuIIOasJKCD3eRsjbbZRRu8Ws27AxyLs92wM9QheHIxHtYPXoC4Gs_mkt20Kckjd4ZkKTz0bKgdTN9iRlmp1gOAMMlb',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);




        // curl -X POST -H "Authorization: Bearer ya29.ElqKBGN2Ri_Uz...HnS_uNreA" -H "Content-Type: application/json" -d '{
        //     "message":{
        //       "notification": {
        //         "title": "FCM Message",
        //         "body": "This is an FCM Message",
        //       },
        //       "token": "bk3RNwTe3H0:CI2k_HHwgIpoDKCIZvvDMExUdFQ3P1..."
        //       }
        //     }' https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send
    }
}
