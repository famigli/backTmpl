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
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Entity\Paziente;

class PazienteController extends AbstractController
{

    /**
     * @Route("/api/paziente",  methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Inscrisci paziente",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Paziente::class, groups={"full"}))
     *     )
     * )
     */
    public function nuovoAction(
        Request $request, 
        EntityManagerInterface $em,
        PazienteManagerSvc $pazienteManagerSvc        
    ): Response
    {
        try {
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
            return $this->json(["paziente" => $paziente]);
                
        } catch (Exception $ex) {
            return $this->json(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/paziente", name="paziente")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PazienteController.php',
        ]);
    }
}
