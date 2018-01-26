<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * @dataProvider newUserProvider
     */
    public function testSignUp($email, $password, $statusCode)
    {
        $client = static::createClient();

        $client->request('POST', '');

        $client->request(
            'POST', '/api/v1/signup', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'plainPassword' => $password
            ])
        );

        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
    }

    public function newUserProvider()
    {
        return [
            ['invalidEmail', 'password', Response::HTTP_BAD_REQUEST], // invalid email
            ['invalidEmail', '', Response::HTTP_BAD_REQUEST], // empty password
            ['', 'password', Response::HTTP_BAD_REQUEST], // email is missing
            ['bob@gmail.com', 'password', Response::HTTP_NO_CONTENT], // correct data
            ['bob@gmail.com', 'password', Response::HTTP_BAD_REQUEST], // duplicate user
        ];
    }


    public function testSignIn()
    {
        $client = static::createClient();

        $client->request(
            'POST', '/api/v1/signin', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'alice@gmail.com',
                'plainPassword' => 'password'
            ])
        );

        //
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    /**
     * @param string $token
     * @depends testSignIn
     */
    public function testSignOut($token)
    {
        $client = static::createClient();

        // should sing out
        $client->request(
            'GET', '/api/v1/signout', [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X-AUTH-TOKEN' => $token]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        // should return 401 as token should be changed
        $client->request(
            'GET', '/api/v1/signout', [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X-AUTH-TOKEN' => $token]
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
