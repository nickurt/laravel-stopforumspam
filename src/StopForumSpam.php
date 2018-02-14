<?php

namespace nickurt\StopForumSpam;

use \GuzzleHttp\Client;
use \nickurt\StopForumSpam\Exception\MalformedURLException;

class StopForumSpam
{
    /**
     * @var string
     */
    protected $apiUrl = 'https://api.stopforumspam.org/api';

    /**
     * @var
     */
    protected $ip;

    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    protected $frequency = 10;

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param $apiUrl
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        if (filter_var($apiUrl, FILTER_VALIDATE_URL) === false) {
            throw new MalformedURLException();
        }

        $this->apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param $frequency
     * @return $this
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * @return bool
     */
    public function IsSpamEmail()
    {
        $response = $this->getResponseData(
            sprintf('%s?email=%s&json',
                $this->getApiUrl(),
                $this->getEmail()
            ));

        $result = json_decode($response);

        if(isset($result->success) && $result->success) {
            if(isset($result->email->appears) && $result->email->appears) {
                if($result->email->frequency >= $this->getFrequency()) {
                    event(new \nickurt\StopForumSpam\Events\IsSpamEmail($this->getEmail()));

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function IsSpamIp()
    {
        $response = $this->getResponseData(
            sprintf('%s?ip=%s&json',
                $this->getApiUrl(),
                $this->getIp()
            ));

        if(isset($result->success) && $result->success) {
            if(isset($result->ip->appears) && $result->ip->appears) {
                if($result->ip->frequency >= $this->getFrequency()) {
                    event(new \nickurt\StopForumSpam\Events\IsSpamIp($ip));

                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getResponseData($url)
    {
        $client = new Client();
        $requestOption = $this->getRequestOption();
        $request = $client->get($url, [$requestOption => $this->toArray()]);

        return $request;
    }

    /**
     * @return string
     */
    protected function getRequestOption()
    {
        return (version_compare(\GuzzleHttp\ClientInterface::VERSION, '6.0.0', '<')) ? 'body' : 'form_params';
    }
}
