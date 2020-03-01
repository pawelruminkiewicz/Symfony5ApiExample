<?php

namespace App\Controller\api;

use App\Entity\Exam;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class ExamController extends AbstractController
{
    /**
     * @Route("/api/exams", name="list_exams", methods={"GET"})
     * 
     * @SWG\Tag(name="exams")
     * @SWG\Response(response=200, description="successful operation")
     * 
     * @SWG\Parameter(name="page", in="query", type="integer")
     * @SWG\Parameter(name="pageSize", in="query", type="integer")
     * 
     * @param Request $request
     * 
    */
    public function listExams(Request $request) {
        $page =  $request->query->get('page');
        $pageSize =  $request->query->get('pageSize');

        $exams = $this->getDoctrine()->getRepository(Exam::class)->findAll();
        if (empty($exams)) {       
            return new JsonResponse('No exams found', Response::HTTP_NOT_FOUND, ['content-type' => 'text/html']);
        }

        $array = array();
        foreach ($exams as &$value) {
            array_push($array, [
                "id" => $value->getId(),
                "points" => $value->getPoints(),
                "teacher" => $value->getTeacher(),
                "description" => $value->getDescription(),
            ]);
        }
        return new JsonResponse(array_slice($array, $page * $pageSize, $pageSize));
    }

    /**
     * @Route("/api/exams", name="add_exam", methods={"POST"})
     * 
     * @SWG\Tag(name="exams")
     * @SWG\Response(response=200, description="successful operation")
     * @SWG\Response(response=409, description="POST has been duplicated")
     * 
     * @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref=@Model(type=Exam::class)),
     * )
     * 
     * @param Request $request
     * 
    */
    public function addExam(Request $request) {
        $data = json_decode($request->getContent(), true);
        $exam = $this->getDoctrine()->getRepository(Exam::class)->findOneBy([
            "points" => $data['points'],
            "teacher" => $data['teacher'],
            "description" => $data['description'],
        ]);
        if($exam) {
            $data = [
                "id" => $value->getId(),
                "points" => $value->getPoints(),
                "teacher" => $value->getTeacher(),
                "description" => $value->getDescription(),
            ];
            return new JsonResponse($data, JsonResponse::HTTP_CONFLICT, ['content-type' => 'application/json']);
        }

        $exam = new Exam();
        $exam->setPoints($data['points']);
        $exam->setTeacher($data['teacher']);
        $exam->setDescription($data['description']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($exam);
        $entityManager->flush();
        return new JsonResponse('Saved new exam with id '.$exam->getId());
     }



     /**
     * @Route("/api/exams/{id}", name="update_exam", methods={"PUT"})
     * 
     * @SWG\Tag(name="exams")
     * @SWG\Response(response=200, description="successful operation")
     * @SWG\Response(response=418, description="precondition failed")
     * @SWG\Response(response=404, description="not found")
     * 
     * @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref=@Model(type=Exam::class)),
     * )
     * 
     * @SWG\Parameter(
     *      name="etag",
     *      in="header",
     *      required=true,
     *      type="string",
     * )
     * 
     * @param Request $request
     * @param int $id
     * 
     */
    public function updateExam(Request $request, int $id) {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $exam = $entityManager->getRepository(Exam::class)->find($id);
        if (!$exam) {       
            return new JsonResponse('No exam found for id '.$id, Response::HTTP_NOT_FOUND, ['content-type' => 'text/html']);
        }

        $computedEtag = $exam->getMD5Shortcut();
        $requestEtag = $request->headers->get('etag');

        if ($requestEtag != $computedEtag) {
            return new JsonResponse('', Response::HTTP_PRECONDITION_FAILED, ['content-type' => 'text/html']);
        }

        $exam->setPoints($data['points']);
        $exam->setTeacher($data['teacher']);
        $exam->setDescription($data['description']);
        $entityManager->flush();
    
        return new JsonResponse('Exam with id '.$id.' updated successfully!');
     }

     /**
     * @Route("/api/exams/{id}", name="delete_exam", methods={"DELETE"})
     * 
     * @SWG\Tag(name="exams")
     * @SWG\Response(response=200, description="successful operation")
     * @SWG\Response(response=404, description="not found")
     * 
     */
    public function deleteExam($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $exam = $entityManager->getRepository(Exam::class)->find($id);
        if (!$exam) {       
            return new JsonResponse('No exam found for id '.$id, Response::HTTP_NOT_FOUND, ['content-type' => 'text/html']);
        }
        $entityManager->remove($exam);
        $entityManager->flush();

        return new JsonResponse('Exam with id '.$id.' deleted');
     }

     /**
     * @Route("/api/exams/{id}", name="show_exam", methods={"GET"})
     * 
     * @SWG\Tag(name="exams")
     * @SWG\Response(response=200, description="successful operation")
     * @SWG\Response(response=304, description="not modified")
     * @SWG\Response(response=404, description="not found")
     * 
     *  @param int $id
     *  @param Request $request
     * 
     */
    public function showExam(int $id, Request $request) {
        $exam = $this->getDoctrine()->getRepository(Exam::class)->find($id);

        if (!$exam) {       
            return new Response('No exam found for id '.$id, Response::HTTP_NOT_FOUND, ['content-type' => 'text/html']);
        }
        
        $response = new Response();
        $response->setContent(json_encode([
            "id" => $exam->getId(),
            "points" => $exam->getPoints(),
            "teacher" => $exam->getTeacher(),
            "description" => $exam->getDescription(),
        ]));
        $response->headers->set('Content-Type', 'application/json');
        $response->setEtag($exam->getMD5Shortcut());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return new Response('', Response::HTTP_NOT_MODIFIED, ['content-type' => 'text/html']);
        }
        return $response;
     }
}