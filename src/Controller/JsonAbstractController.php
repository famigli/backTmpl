<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonAbstractController extends AbstractController {


    /**
     * Returns a JsonResponse che gestisce correttamente i dateTime e le annotation .
     */
    protected  function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $objNormalizer = new ObjectNormalizer ($classMetadataFactory, null, null, null, null, null, $context);
        
        
        #Istanziare un DateTimeNormalizer per gestire correttamente i campi dati, specificando il formato desiderato
        $defaultDateTimeContext = ['datetime_format' => 'c'];
        $dtNormalizer = new DateTimeNormalizer($defaultDateTimeContext);
        
        $encoder = new JsonEncoder();
        
        #Istanziare il Serializer specificando i normalizer predisposti
        #Attenzione! L'ordine è vincolante, se imposto il DateTimeNormalizer come secondo elemento gestisce tutto l'ObjectNormalizer
        $serializer = new Serializer([$dtNormalizer, $objNormalizer], [$encoder]);
        
        #Per ottenere un array presisposto per l'encoding:
        $json = $serializer->serialize($data, 'json', $context);

        return new JsonResponse($json, $status, $headers, true);
    }


}