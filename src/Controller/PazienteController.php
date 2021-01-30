<?php

namespace App\Controller;

use App\Service\Model\PazienteManagerSvc;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Paziente;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/paziente")
 * @OA\Info(title="Paziente API", version="1.0.0") 
 */
class PazienteController extends AbstractController
{

    /**
     * @Route("",  methods={"POST"}, format="json")
     * @OA\Post(
     *     path="/api/paziente",
     *     summary="Crea un nuovo paziente",
     *     description="Crea un nuovo paziente2",
     *     @OA\RequestBody(
     *         description="Client side search object",
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="application/json",                 
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="dataNascita",
     *                      type="string",
     *                      format="date-time"
     *                  ),
     *                  @OA\Property(
     *                      property="cognome",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="nome",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="sesso",
     *                      type="string"
     *                  ),
     *                  example={"dataNascita": "1985-04-12T23:20:50.52Z", "nome": "Jessica", "cognome": "Smith", "sesso": "F"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\Schema(ref="#/components/schemas/SearchResultObject")   
     *     ), 
     *     @OA\Response(
     *         response=404,
     *         description="Could Not Find Resource"
     *     )
     * )
     * 
     */
    public function nuovoAction(
        Request $request, 
        EntityManagerInterface $em,
        PazienteManagerSvc $pazienteManagerSvc,
        SerializerInterface $serializer      
    ): Response
    {        
        try {

            $this->denyAccessUnlessGranted('ROLE_USER', null, 'L\'utente non dispone del ruolo ROLE_USER');

            if (!$jsonData = json_decode($request->getContent()))
                throw new Exception ("Payload mancante");
            $dataNascita = DateTime::createFromFormat(DateTimeInterface::ATOM, $jsonData->dataNascita);
            if ($dataNascita === FALSE)
                throw new Exception('dataNascita non corretta');
            if (!isset($jsonData->cognome))
                throw new Exception('Cognome non specificato');
            if (!isset($jsonData->nome))
                throw new Exception('Nome non specificato');
            if (!isset($jsonData->sesso))
                throw new Exception('Sesso non specificato');

            try {
                $em->getConnection()->beginTransaction();
                $paziente = $pazienteManagerSvc->getNew($jsonData->cognome, $jsonData->nome, $dataNascita, $jsonData->sesso);
                $em->getConnection()->commit();
            } catch (Exception $ex) {
                $em->getConnection()->rollBack();
                throw $ex;
            }

            #volendo specificare ulteriori opzioni per i normalizer
            $context = [
                'datetime_format' => 'd/m/Y',
                ObjectNormalizer::GROUPS  => ['a'],
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['cognome']
            ];
            return $this->json($paziente, Response::HTTP_OK, [], $context);
            #oppure
            $json = $serializer->serialize($paziente, 'json', $context);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        } catch (Exception $ex) {
            return $this->json((object)['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("", name="paziente", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PazienteController.php',
        ]);
    }
}
