<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\SignInType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/api/v1/signin", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return an user"
     * )
     *
     * @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="User Model",
     *         required=true,
     *         @Model(type=AppBundle\Form\SignInType::class)
     * )
     */
    public function signInAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if (!($request->getContentType() == 'application/json' || $request->getContentType() == 'json')) {
            return new JsonResponse('API supports only application/json', Response::HTTP_BAD_REQUEST);
        }

        // get data
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(SignInType::class, new User());
        $form->submit($data);

        /**
         * @var \FOS\UserBundle\Doctrine\UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByUsernameOrEmail($form->get('email')->getData());

        if ($user !== null && $encoder->isPasswordValid($user, $form->get('plainPassword')->getData())) {
            $user->generateApiKey();
            $userManager->updateUser($user);

            return new JsonResponse([
                'token' => $user->getApiKey()
            ]);
        } else {
            return new JsonResponse('', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     *
     * @Route("/api/v1/signup", methods={"POST"})
     *
     * @SWG\Response(
     *     response=204,
     *     description="User has been created"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="Missing data"
     * )
     *
     * @SWG\Response(
     *     response=409,
     *     description="User already exists"
     * )
     *
     * @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="User Model",
     *         required=true,
     *         @Model(type=AppBundle\Form\SignInType::class)
     * )
     */
    public function signUpAction(Request $request)
    {
        if (!($request->getContentType() == 'application/json' || $request->getContentType() == 'json')) {
            return new JsonResponse('API supports only application/json', Response::HTTP_BAD_REQUEST);
        }

        // get data
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(SignInType::class, $user);
        $form->submit($data);

        /**
         * @var \FOS\UserBundle\Doctrine\UserManager $userManager
         */
        $userManager = $this->get('fos_user.user_manager');

        if (!$form->isValid()) {
            return new JsonResponse((string)$form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        // enable the user
        $user->setEnabled(true);

        $userManager->updateUser($user);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     *
     * @Route("/api/v1/signout", methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Response(
     *     response=204,
     *     description="Sing out"
     * )
     *
     * @SWG\Response(
     *     response=401,
     *     description="is not signed in"
     * )
     *
     * @SWG\Parameter(
     *         name="X-AUTH-TOKEN",
     *         in="header",
     *         description="API token",
     *         required=true,
     *         type="string"
     * )
     */
    public function signOutAction()
    {
        $user = $this->getUser();
        $user->generateApiKey();
        $this->get('fos_user.user_manager')->updateUser($user);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}
