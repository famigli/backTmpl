<?php
namespace App\Service\Model;

use App\Exception\ValidationException;
use App\Entity\Paziente;
use App\Entity\Ricovero;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class RicoveroManagerSvc {
    
    const EVENT_POST_CREA = 'Ricovero.post_crea';
    const EVENT_POST_DIMETTI = 'Ricovero.post_dimetti';
    const EVENT_POST_TRASFERISCI = 'Ricovero.post_trasferisci';
    

    protected $em;
    protected $validator;
    protected $dispatcher;
    
    public function __construct(
        EntityManagerInterface $em, 
        ValidatorInterface $validator,
        EventDispatcherInterface $dispatcher
    ){
        $this->em = $em;
        $this->validator = $validator;
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * getById
     *
     * @param  mixed $id
     * @return Ricovero
     */
    public function getById($id){
        return $this->em->getRepository(Ricovero::class)->find($id);
    }

    public function getByDataReparto($data, $reparto) {

    }
    
    public function getNew(DateTime $data, $reparto, Paziente $paziente) {
        
        $ricovero = new Ricovero();
        $ricovero->setData($data);
        $ricovero->setReparto($reparto);
        $ricovero->setPaziente($paziente);
        $this->validate($ricovero);
        $this->em->persist($ricovero);
        $this->em->flush();   
        
        $evento = new GenericEvent($ricovero, []);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_CREA);

        return $ricovero;     
    }

    
    public function trasferisci(Ricovero $ricovero, $reparto) {
        $ricovero->setReparto($reparto);
        $this->validator->validate($ricovero);
        $this->em->flush();
        
        $evento = new GenericEvent($ricovero, []);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_TRASFERISCI);

        //se questa azione necessita di eseguire altre azioni sullo stesso oggetto le chiamo direttamente 
        //comunque dopo l'evento trasferisci
        //esempio assurdo
        //$this->dimetti($ricovero, new DateTime());

    }

    public function dimetti(Ricovero $ricovero, $dataDimissione){

        $preState = [
            "dataDimissione" => $ricovero->getDataDimissione()
        ];
        //oppure clonazione deep di $ricovero

        $ricovero->setDataDimissione($dataDimissione);
        $this->validator->validate($ricovero);
        $this->em->flush();

        /**
         * Implementazione sbagliata 
         */
        //$integrazionePleiade->accodaDimissione(...)
        //$integrazioneRepository->accodaDimissione(...)
        //$generazioneSdo->accodaDimissione(..)
        //...
        /* oppure in caso di diverse integrazioni a seconda dell'asl in cui verrà installato
        switch ($installazione) {
            case "AOUP":
                $integrazionePleiadeAoup->accodaDimissione(...)
            case "AOUS"
                $integrazionePleiadeAous->accodaDimissione(...)
            case "GROSSETO"
                $integrazionePleiadeGrosseto->accodaDimissione(...)
            }
        */

        /**
         * Implementazione che garantisce isolamento e disaccoppiamento tra i servizi 
         * utilizzando un EventDispatcher che implementa un pattern publish & subscribe sincrono 
         */

        $evento = new GenericEvent($ricovero, $preState);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_DIMETTI);
        return $this;
    }

    protected function validate(Ricovero $ricovero){
        $violations = $this->validator->validate($ricovero);
        //aggiungere eventuale validazione semantica con un ConstraintValidator
        if (count($violations) > 0)
            throw new ValidationException($violations);        
    }

}
