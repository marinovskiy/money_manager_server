<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 9:23 AM
 */

namespace AppBundle\Controller\Api;

use AppBundle\AppBundle;
use AppBundle\Entity\Device;
use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use AppBundle\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api")
 */
class ApiAuthController extends Controller
{
    /**
     * @Route("/registration", name="api_user_registration")
     * @Method({"POST"})
     */
    public function registrationAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->submit($data['user']);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($form->getErrors() as $err) {
            echo $err->getMessage();
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $this->get('app.user_manager')->registerUser($user);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);

            $this->updateApiKeyAndCheckDevice($user, $request->headers->get('platformType'), $request->headers->get('udid'));
//            $this->updateApiKeyAndCheckDevice($user, $form->get('platformType')->getData(), $form->get('udid')->getData());

            $em->flush();

            return new JsonResponse($user);
        }

        throw new HttpException(400, 'Invalid data');
    }

    /**
     * @Route("/login", name="api_user_login")
     * @Method({"POST"})
     */
    public function loginAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->info('LOGIN ACTION');

        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer(null);
        $normalizer->setIgnoredAttributes(array('email'));
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->submit($data['user']);

        foreach ($form->getErrors() as $err) {
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->loadUserByEmail($form->get('email')->getData());

            if ($user) {
                if (password_verify($form->get('password')->getData(), $user->getPassword())) {
                    $this->updateApiKeyAndCheckDevice(
                        $user,
                        $request->headers->get('platformType'),
                        $request->headers->get('udid')
                    );
                    $this->getDoctrine()->getManager()->flush();

                    return new JsonResponse($user);
                }
            } else {
                throw new HttpException(404, 'User not found');
            }
        }

        throw new HttpException(403, 'something other');
    }

    private function updateApiKeyAndCheckDevice($user, $type, $udid)
    {
        $em = $this->getDoctrine()->getManager();
        $deviceRepository = $em->getRepository('AppBundle:Device');
        $device = $deviceRepository->findOneBy(['udid' => $udid]);

        if (!$device) {
            $device = new Device();
            $em->persist($device);
        }

        $device->setUser($user)
            ->setUdid($udid)
            ->setPlatformType($type)
            ->setApiKey();

        $user->setApiKey($device->getApiKey());
    }
}