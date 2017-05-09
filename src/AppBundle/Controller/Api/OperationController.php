<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 1:41 PM
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Operation;
use AppBundle\Form\NewOperationType;
use AppBundle\Manager\OperationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OperationController extends Controller
{
    /*
     * @Route("/operations/new" name="operations_new")
     * @Method("POST")
     */
    public function operationsNewAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        $operation = new Operation();
        $form = $this->createForm(NewOperationType::class, $operation);
        $form->submit($data['operation']);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($form->getErrors() as $err) {
            echo $err->getMessage();
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
//            $this->get('app.expense_manager')->addExpense($operation);
            $this->getDoctrine()->getManager()->persist($operation);
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(
                $serializer->serialize(
                    $operation,
                    'json'
                ),
                200
            );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        throw new HttpException(400, 'Invalid data');
    }

    /*
     * @Route("/operations" name="operations_list")
     * @Method("GET")
     */
    public function operationsListAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();
        $operations = $em->getRepository(Operation::class)->loadAllOperations();

        $response = new Response(
            $serializer->serialize(
                $operations,
                'json'
            ),
            200
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}