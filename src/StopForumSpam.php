<?php

namespace nickurt\StopForumSpam;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use nickurt\StopForumSpam\Events\IsSpamEmail;
use nickurt\StopForumSpam\Events\IsSpamIp;
use nickurt\StopForumSpam\Events\IsSpamUsername;
use nickurt\StopForumSpam\Exception\MalformedEmailException;
use nickurt\StopForumSpam\Exception\MalformedIPException;
use nickurt\StopForumSpam\Exception\MalformedURLException;

class StopForumSpam
{
    /** @var string */
    protected $apiUrl = 'https://api.stopforumspam.org/api';

    /** @var string */
    protected $addToDatabaseUrl = 'https://www.stopforumspam.com/add.php';

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $email;

    /** @var int */
    protected $frequency = 10;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $username;

    /** @var string */
    protected $evidence;

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamEmail()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getEmail()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?email=%s&json',
                    $this->getApiUrl(),
                    $this->getEmail()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['email']['appears']) && $result['email']['appears']) {
                if ($result['email']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamEmail($this->getEmail(), $result['email']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string  $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array|mixed
     */
    protected function getResponseData($url)
    {
        return Http::get($url)->json();
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
    * @return string
    */
    public function getAddToDatabaseUrl(): string
    {
        return $this->addToDatabaseUrl;
    }

    /**
    * @param string $addToDatabaseUrl
    * @return $this
    */
    public function setAddToDatabaseUrl(string $addToDatabaseUrl): static
    {
        if (filter_var($addToDatabaseUrl, FILTER_VALIDATE_URL) === false) {
            throw new MalformedURLException();
        }

        $this->addToDatabaseUrl = $addToDatabaseUrl;

        return $this;
    }

    /**
     * @param  string  $apiUrl
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
    * @return string
    */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
    * @param string $apiKey
    * @return $this
    */
    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param  int  $frequency
     * @return $this
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamIp()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getIp()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?ip=%s&json',
                    $this->getApiUrl(),
                    $this->getIp()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['ip']['appears']) && $result['ip']['appears']) {
                if ($result['ip']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamIp($this->getIp(), $result['ip']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param  string  $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isSpamUsername()
    {
        $result = cache()->remember('laravel-stopforumspam-'.Str::slug($this->getUsername()), 10, function () {
            return $this->getResponseData(
                sprintf('%s?username=%s&json',
                    $this->getApiUrl(),
                    $this->getUsername()
                ));
        });

        if (isset($result['success']) && $result['success']) {
            if (isset($result['username']['appears']) && $result['username']['appears']) {
                if ($result['username']['frequency'] >= $this->getFrequency()) {
                    event(new IsSpamUsername($this->getUsername(), $result['username']['frequency']));

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param  string  $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
    */
    public function getEvidence(): ?string
    {
        return $this->evidence;
    }

    /**
    * @param string $evidence
    * @return $this
    */
    public function setEvidence(string $evidence): static
    {
        $this->evidence = $evidence;

        return $this;
    }

    /**
     * POST to the given URL and return the HTTP status code.
     * Fields are UTF-8 encoded and urlencoded as required by the SFS API.
     *
     * @param string $url
     * @param array  $fields
     * @return int
     * @throws \Exception
     */
    protected function getResponseCode(string $url, array $fields): int
    {
        // Urlencode all values to satisfy the SFS requirement
        $encodedFields = http_build_query(
            array_map(fn($v) => urlencode((string) $v), $fields)
        );

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->send('POST', $url, [
                'body' => $encodedFields,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('StopForumSpam request failed: ' . $e->getMessage());
        }

        return $response->status();
    }

    /**
     * Submit a spam report to Stop Forum Spam.
     * Requires api_key, ip, email, and username. Evidence is optional.
     *
     * See "Adding to the Database": https://www.stopforumspam.com/usage
     *
     * @return bool
     * @throws \Exception
     * @throws MalformedEmailException
     */
    public function addToDatabase(): bool
    {
        if (empty($this->getApiKey())) {
            throw new Exception('StopForumSpam API key is required.');
        }

        if (empty($this->getIp())) {
            throw new Exception('ip is required.');
        }

        if (filter_var($this->getIp(), FILTER_VALIDATE_IP) === false) {
            throw new MalformedIPException();
        }

        if (empty($this->getEmail())) {
            throw new Exception('email is required.');
        }

        if (filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL) === false) {
            throw new MalformedEmailException();
        }

        if (empty($this->getUsername())) {
            throw new Exception('username is required.');
        }

        $fields = [
            'api_key'  => $this->getApiKey(),
            'ip_addr'  => $this->getIp(),
            'email'    => $this->getEmail(),
            'username' => mb_convert_encoding($this->getUsername(), 'UTF-8'),
        ];

        if (!empty($this->getEvidence())) {
            $fields['evidence'] = mb_convert_encoding($this->getEvidence(), 'UTF-8');
        }

        return $this->getResponseCode($this->getAddToDatabaseUrl(), $fields) === 200;
    }
}
