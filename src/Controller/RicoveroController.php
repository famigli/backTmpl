<?php

namespace App\Controller;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Service\Model\PazienteManagerSvc;
use App\Service\Model\RicoveroManagerSvc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RicoveroController extends AbstractController
{
    

    /**
     * @Route("/ricovero", methods={"POST"})
     */
    public function ricoveraAction(
        Request $request, 
        EntityManagerInterface $em,
        RicoveroManagerSvc $ricoveroManagerSvc,
        PazienteManagerSvc $pazienteManagerSvc        
    ): Response
    {
        try {
            $jsonData = json_decode($request->getContent());
            $data = DateTime::createFromFormat(DateTimeInterface::ATOM, $jsonData->data);
            if ($data === FALSE)
                throw new Exception('Data non corretta');
            $paziente = $pazienteManagerSvc->getById($jsonData->idPaziente);
            if ($paziente === null)
                throw new Exception('Paziente non specificato');
            if (!isset($jsonData->telefono))
                throw new Exception('Telefono non specificato');
            if (!isset($jsonData->reparto))
                throw new Exception('Reparto non specificato');
            
            try {
                $em->getConnection()->beginTransaction();
                $ricovero = $ricoveroManagerSvc->getNew($data, $jsonData->reparto, $paziente);
                $pazienteManagerSvc->setTelefono($paziente, $jsonData->telefono);    
                $em->getConnection()->commit();
            } catch (Exception $ex) {
                $em->getConnection()->rollBack();
                throw $ex;
            }
            return $this->json(["ricovero" => $ricovero]);
            
        } catch (Exception $ex) {
            return $this->json(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * dimetti
     *
     * @Route("/ricovero/dimetti", methods={"PUT"})
     * @return void
     */
    public function dimettiAction(
        Request $request, 
        EntityManagerInterface $em,
        RicoveroManagerSvc $ricoveroManagerSvc
    ) {
        try {
            $jsonData = json_decode($request->getContent());
            $data = DateTime::createFromFormat(DateTimeInterface::ATOM, $jsonData->data);
            if ($data === FALSE)
                throw new Exception('Data non corretta');
            $ricovero = $ricoveroManagerSvc->getById($jsonData->idRicovero);
            if ($ricovero === null)
                throw new Exception('Ricovero non specificato');
            
            try {
                $em->getConnection()->beginTransaction();
                $ricoveroManagerSvc->dimetti($ricovero, $data);
                $em->getConnection()->commit();
            } catch (Exception $ex) {
                $em->getConnection()->rollBack();
                throw $ex;
            }
            return $this->json(["ricovero" => $ricovero]);
            
        } catch (Exception $ex) {
            return $this->json(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/ricovero")     
     */
    public function index(Request $request): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RicoveroController.php',
            'method' => $request->getMethod()
        ]);
    }

}
