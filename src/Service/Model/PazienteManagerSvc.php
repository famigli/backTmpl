<?php
namespace App\Service\Model;

use AdibaBundle\Exception\ValidationException;
use App\Entity\Paziente;
use App\Entity\Ricovero;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PazienteManagerSvc implements EventSubscriberInterface{

    const EVENT_POST_CREA = 'Paziente.post_crea';
    const EVENT_POST_AGGIORNA_TELEFONO = 'Paziente.post_aggiorna_telefono';
    const EVENT_POST_AGGIORNA_RESIDENZA = 'Paziente.post_aggiorna_residenza';
    const EVENT_POST_AGGIORNA_IS_RICOVERATO = 'Paziente.post_aggiorna_is_ricoverato';

    protected $em;
    protected $validator;
    protected $dispatcher;
        
    /**
     * __construct
     *
     * @return void
     */
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
     * @param  integer $id
     * @return Paziente
     */
    public function getById($id) {
        return $this->em->getRepository(Paziente::class)->find($id);
    }
        
    /**
     * getNew
     *
     * @param  string $cognome
     * @param  string $nome
     * @param  mixed $dataNascita
     * @param  string $sesso
     * @return Paziente
     */
    public function getNew($cognome, $nome, DateTime $dataNascita, $sesso) {
        $paziente = new Paziente();
        
        $paziente->setCognome($cognome);
        $paziente->setNome($nome);
        $paziente->setDataNascita($dataNascita);
        $paziente->setSesso($sesso);
        $paziente->setIsRicoverato(false);
        $this->validate($paziente);
        $this->em->persist($paziente);
        $this->em->flush();   
        
        $evento = new GenericEvent($paziente, []);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_CREA);

        return $paziente;     
    }

    /**
     * aggiornaTelefono
     *
     * @param  Paziente $paziente
     * @param  string $telefono
     * @return PazienteManagerSvc
     */
    public function setTelefono(Paziente $paziente, $telefono) {
        
        $preState = [
            "telefono" => $paziente->getTelefono()
        ];

        $paziente->setTelefono($telefono);
        $this->validator->validate($paziente);        
        $this->em->flush();

        $evento = new GenericEvent($paziente, $preState);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_AGGIORNA_TELEFONO);

        return $this;
    }
    
    /**
     * aggiornaResidenza
     *
     * @param  Paziente $paziente
     * @param  string $indirizzoResidenza
     * @param  string $comuneResidenza
     * @return PazienteManagerSvc
     */
    public function setResidenza(Paziente $paziente, $indirizzoResidenza, $comuneResidenza){
        $preState = [
            "indirizzoResidenza" => $paziente->getIndirizzoResidenza(),
            "comuneResidenza" => $paziente->getComuneResidenza()
        ];

        $paziente->setIndirizzoResidenza($indirizzoResidenza);
        $paziente->setComuneResidenza($comuneResidenza);
        $this->validator->validate($paziente);
        $this->em->flush();

        $evento = new GenericEvent($paziente, $preState);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_AGGIORNA_RESIDENZA);
        return $this;
    }
    
    /**
     * setIsRicoverato
     *
     * @param  Paziente $paziente
     * @param  boolean $isRicoverato
     * @return PazienteManagerSvc
     */
    public function setIsRicoverato(Paziente $paziente, $isRicoverato){
        $preState = [
            "isRicoverato" => $paziente->getIsRicoverato()            
        ];

        $paziente->setIsRicoverato($isRicoverato);
        
        $this->validator->validate($paziente);
        $this->em->flush();

        $evento = new GenericEvent($paziente, $preState);
        $this->dispatcher->dispatch($evento, self::EVENT_POST_AGGIORNA_IS_RICOVERATO);
        return $this;
    }
    
    /**
     * validate
     *
     * @param  Paziente $paziente
     * @return void
     * @throws ValidationException
     */
    protected function validate(Paziente $paziente){
        $violations = $this->validator->validate($paziente);
        //aggiungere eventuale validazione semantica in un ConstraintValidator
        if (count($violations) > 0)
            throw new ValidationException($violations);        
    }
    

    /**
     * Events subscriptions
     */
    

    /**
     * getSubscribedEvents
     *
     * @return void
     */
    public static function getSubscribedEvents()
    {
        return [
            RicoveroManagerSvc::EVENT_POST_CREA => 'onRicoveroPostCrea',
            RicoveroManagerSvc::EVENT_POST_DIMETTI => 'onRicoveroPostDimetti'
        ];
    }
    
    /**
     * onRicoveroPostCrea
     *
     * @param  GenericEvent $event
     * @return void
     */
    public function onRicoveroPostCrea(GenericEvent $event){
        $ricovero = $event->getSubject();
        $paziente = $ricovero->getPaziente();

        $this->setIsRicoverato($paziente, true);
        
    }
    
    /**
     * onRicoveroPostDimetti
     *
     * @param  GenericEvent $event
     * @return void
     */
    public function onRicoveroPostDimetti(GenericEvent $event){
        $ricovero = $event->getSubject();
        $paziente = $ricovero->getPaziente();

        $this->setIsRicoverato($paziente, false);
    }

}
