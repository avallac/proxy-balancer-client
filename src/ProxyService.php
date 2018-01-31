<?php

namespace AVAllAC\ProxyBalancerClient;

use Httpful\Mime;
use Httpful\Request;

class ProxyService
{
    protected $apiUrl;
    protected $user;
    protected $password;

    public function __construct($apiUrl, $user, $password)
    {
        $this->apiUrl = $apiUrl;
        $this->user = $user;
        $this->password = $password;
    }

    public function getProxy($service)
    {
        $count = 0;
        $answer = $this->get($service);
        while (!$answer) {
            $answer = $this->get($service);
            sleep(1);
            $count++;
        }
        return $answer;
    }

    public function complaint($service, $auth)
    {
        $info = Request::post($this->apiUrl . '/complaint/' . $service)
            ->body('uri='.urlencode($auth))->sendsType(Mime::FORM)
            ->authenticateWith($this->user, $this->password)->send();
        return $info->body;
    }

    public function report($service, $auth, $runTime)
    {
        $info = Request::post($this->apiUrl . '/report/' . $service)
            ->body('uri='.urlencode($auth).'&result='.$runTime)->sendsType(Mime::FORM)
            ->authenticateWith($this->user, $this->password)->send();
        return $info->body;
    }

    protected function get($service)
    {
        $answer = Request::get($this->apiUrl . '/get/' . $service)
            ->authenticateWith($this->user, $this->password)->send();
        return $answer->body;
    }
}
