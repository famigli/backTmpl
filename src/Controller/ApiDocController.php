<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiDocController extends AbstractController {

    /**
     * @Route("/apidoc",  methods={"get"}, format="json")
     */
    public function apiDocAction(){


        $openapi = \OpenApi\scan(__DIR__ . '/../');
        header('Content-Type: application/x-yaml');
        $json = $openapi->toJson();
        return new JsonResponse($json, Response::HTTP_OK, [], true); 

    }

}