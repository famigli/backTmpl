<?php
namespace App\Service\Integrazioni;

use AdibaBundle\Exception\ValidationException;
use App\Entity\Paziente;
use App\Entity\Rfc249Out;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Rfc249OutManagerSvc
 * 
 * Gli eventi vengono sottoscritti come listener in services_xxx.yaml
 * in questo modo l'integrazione può essere abilitata o meno in base all'ambiente
 * quindi in base all'installazione
 */
class Rfc249OutManagerSvc {

    const EVENT_POST_CREA = 'Rfc249Out.post_crea';

    const STATO_NUOVO = "NW";

    protected $em;
    protected $validator;
    protected $dispatcher;
    protected $params;
        
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        EntityManagerInterface $em, 
        ValidatorInterface $validator,
        EventDispatcherInterface $dispatcher,
        ParameterBagInterface $params
    ){
        $this->em = $em;
        $this->validator = $validator;
        $this->dispatcher = $dispatcher;
        $this->params = $params;

    }

    
    /**
     * getNew
     *
     * @param  string $messaggio
     * @return Rfc249Out
     */
    public function getNew($messaggio) {
        
        $rfc249out = new Rfc249Out();
        $rfc249out->setMessaggio($messaggio);
        $rfc249out->setData(new DateTime());
        $rfc249out->setStato(self::STATO_NUOVO);

        $this->validate($rfc249out);
        $this->em->persist($rfc249out);
        $this->em->flush();
        
        $evento = new GenericEvent($rfc249out, []);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_CREA);

        return $rfc249out;     
    }
    
     
    /**
     * onPostAggiornaTelefono
     *
     * @param  mixed $event
     * @return void
     */
    public function onPostAggiornaTelefono(GenericEvent $event){
        $paziente = $event->getSubject();
        $this->getNew($this->creaMessaggioRfc249($paziente));
    }
    
    /**
     * onPostAggiornaResidenza
     *
     * @param  mixed $event
     * @return void
     */
    public function onPostAggiornaResidenza(GenericEvent $event){
        $paziente = $event->getSubject();
        $this->getNew($this->creaMessaggioRfc249($paziente));
    }
    
    /**
     * creaMessaggioRfc249
     *
     * @param  Paziente $paziente
     * @return string
     */
    protected function creaMessaggioRfc249(Paziente $paziente){
        $xml = new DOMDocument();
        $xml->appendChild($xml->createElement("id", $paziente->getId()));
        $xml->appendChild($xml->createElement("cognome", $paziente->getCognome()));
        $xml->appendChild($xml->createElement("nome", $paziente->getNome()));
        $xml->appendChild($xml->createElement("telefono", $paziente->getTelefono()));
        
        return $xml->saveXML();

    }
    
    /**
     * validate
     *
     * @param Rfc249Out $rfc249Out
     * @return void
     */
    protected function validate(Rfc249Out $rfc249Out){
        $violations = $this->validator->validate($rfc249Out);
        //aggiungere eventuale validazione semantica con un ConstraintValidator
        if (count($violations) > 0)
            throw new ValidationException($violations);        
    }

}