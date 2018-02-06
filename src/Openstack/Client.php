<?php
namespace Wiard\Openstack;

use Dotenv\Dotenv;
use GuzzleHttp;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class Client
{
    
    private $identityUrl;
    private $username;
    private $password;
    private $debug;
    
    public function __construct()
    {
        // TODO make this nice..
        $env = new Dotenv('./');
        $env->load();
        
        $this->identityUrl = getenv('identity_url');
        $this->username    = getenv('username');
        $this->password    = getenv('password');
        
        $this->debug = true;
        
        
    }
    
    public function request()
    {
        
        /**
         * Basic test object
         *
         * Be fucking careful since methods is a bloody array.. seriously.
         */
        $data['auth']['identity']['methods'][]                          = 'password';
        $data['auth']['identity']['password']['user']['name']           = $this->username;
        $data['auth']['identity']['password']['user']['password']       = $this->password;
        $data['auth']['identity']['password']['user']['domain']['name'] = 'Default';
        
        
        /**
         * Middleware handler to debug raw request
         */
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            $contentsRequest = (string)$request->getBody();
            print_r($contentsRequest);
            
            return $request;
        }));
        
        if ($this->debug === true) {
            $options = ['handler' => $stack];
        } else {
            $options = [];
        }
        
        $client = new GuzzleHttp\Client($options);
        
        try {
            
            $response = $client->post($this->identityUrl . '/auth/tokens', [
                RequestOptions::JSON => $data,
            ]);
            
            $body   = $response->getBody();
            $header = $response->getHeaders();
            echo "<pre>";
            var_dump($body);
            var_dump($header);
            
            /**
             * In the header
             *
             *  ["X-Subject-Token"]=>
             *   array(1) {
             *       [0]=>
             *       string(183) "string-with-the-token"
             *   }
             */
            
            die();
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            echo "<pre>";
            print_r($e->getResponse()->getBody()->getContents());
        } catch (GuzzleHttp\Exception\ServerException $e) {
            echo "<pre>";
            print_r($e->getResponse()->getBody()->getContents());
        }
    }
}