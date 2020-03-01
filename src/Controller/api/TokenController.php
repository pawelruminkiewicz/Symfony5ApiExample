<?php

namespace App\Controller\api;


use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Token;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Acme\FooBundle\Validation\Constraints\MyComplexConstraint;

use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TokenController extends AbstractController
{

    /**
     * @Route("/api/token", name="get_token", methods={"POST"})
     * 
     * @SWG\Tag(name="token")
     * @SWG\Response(response=200, description="successful operation")
     * 
     * 
     * @param Request $request
     * 
     */
    public function getToken(Request $request) {
        $data = json_decode($request->getContent(), true);
       
        if (!$request) {
            return $this->respondValidationError('Please provide a valid request!');
        }

        $token = new Token();
      
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($token);
        $entityManager->flush();
        
        return new JsonResponse($token->getTokenId());
    }
}