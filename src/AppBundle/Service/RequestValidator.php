<?php

namespace AppBundle\Service;

//use AppBundle\ApiUtils\ApiException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    const TYPE_JSON = 'json';

    private $validator;
    private $serializer;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param AbstractType $object
     * @param Request $request
     * @param string $type
     * @return object
     * @internal param string $classPath
     */
    public function serializeAndValidate(AbstractType $object, Request $request, string $type = 'json')
    {
        /** @var $classPath $requestBody */
        $requestBody = $this->serializer->deserialize(
            $request->getContent(), get_class($object), $type, ['object_to_populate' => $object]
        );
        //======VALIDATE REQUEST OBJECT========
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validate($requestBody);
        if (count($errors) > 0) {
            $errorResponseArray = [];
            foreach ($errors as $error) {
                $errorResponseArray[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new ApiException(
                'error validating request',
                Response::HTTP_BAD_REQUEST, $errorResponseArray
            );
        }

        return $requestBody;
    }

}