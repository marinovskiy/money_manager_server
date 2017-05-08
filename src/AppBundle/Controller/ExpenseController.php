<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 9:42 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Expense;
use AppBundle\Form\AddExpenseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ExpenseController extends Controller
{
    /*
     * @Route("/expense/add" name="add_expense")
     * @Method("POST")
     */
    public function addExpenseAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        $expense = new Expense();
        $form = $this->createForm(AddExpenseType::class, $expense);
        $form->submit($data['expense']);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($form->getErrors() as $err) {
            echo $err->getMessage();
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.expense_manager')->addExpense($expense);
            $this->getDoctrine()->getManager()->persist($expense);
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(
                $serializer->serialize(
                    $expense,
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
     * @Route("/expense/{expense_id}" name="edit_expense")
     * @Method("PUT")
     */
    public function editExpenseAction(Request $request)
    {

    }

    /*
     * @Route("/expense/{expense_id}" name="delete_expense")
     * @Method("DELETE")
     */
    public function deleteExpenseAction(Request $request)
    {

    }

    /*
     * @Route("/expenses" name="expense_list")
     * @Method("GET")
     */
    public function expenseListAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();
        $expenses = $em->getRepository(Expense::class)->loadAllExpenses();

        $response = new Response(
            $serializer->serialize(
                $expenses,
                'json'
            ),
            200
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}