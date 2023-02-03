<?php


namespace App\Helper;


use App\Http\Middleware\CloudFlare;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GuzzleHelper
{
    private $client;
    private $headers;
    private $options;
    private $cookieFile;
    private $handler;

    public function __construct(string $baseUrl, $debug = false)
    {
        $this->options = [
            'defaults' => [
                'verify' => false
            ],
            'timeout' => 140,
            'connect_timeout' => 140,
            'base_uri' => $baseUrl,
            'debug' => $debug,
//            'decode_content' => false,
            'allow_redirects' => [
                'max'             => 10,
                'strict'          => false,
                'referer'         => true,
                'protocols'       => ['http', 'https'],
                'track_redirects' => true
            ],
        ];

        $this->headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:80.0) Gecko/20100101 Firefox/80.0',
        ];

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                Log::channel('guzzlelog'),
                new MessageFormatter('Req Body: {request}')
            )
        );

        $this->handler = $stack;
    }

    public function setCookieFile(string $name)
    {
        $this->cookieFile = "session/".$name.".txt";
        $this->options['cookies'] = new FileCookieJar($this->cookieFile, TRUE);
    }


    /**
     * @param string $name
     * @return CookieJar
     */
    public function getCookieJarFromFile(string $name): CookieJar
    {
        $setCookie = null;
        $cookieData = json_decode(file_get_contents(public_path('session/'.$name.'.txt')));
        foreach ($cookieData as $cookie) {
            $cookie = json_decode(json_encode($cookie), TRUE);
            $setCookie = new SetCookie($cookie);
        }

        $jar = new CookieJar();
        if (!is_null($setCookie))
            $jar->setCookie($setCookie);

        return $jar;
    }

    public function setCookieFromFile(string $name)
    {
        $this->options['cookies'] = $this->getCookieJarFromFile($name);
    }

    public function deleteCookie(string $name, bool $cache)
    {
        $this->cookieFile = "session/".$name.".txt";
        $path = public_path()."/".$this->cookieFile;
        if ($cache === true)
        {
            Cache::put($name,$path,2);
        }
        else
        {
            if(file_exists($path))
                unlink($path);
        }

        $this->setCookieFromFile($name);
    }

    public function setHeaders(array $data)
    {
        foreach ($data as $key => $value)
        {
            $this->headers[$key] = $value;
        }
    }

    public function setProxy(string $data)
    {
        $this->options['proxy'] = $data;
    }

    public function init()
    {
        $this->client = new Client($this->options);
        $this->client->getConfig('handler')->push(CloudFlare::create());
    }

    public function get($url)
    {
        $this->setHeaders([
            'Upgrade-Insecure-Requests' => '1',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        ]);

        $getHeaders = $this->headers;

        if (isset($getHeaders['X-CSRF-TOKEN']))
            unset($getHeaders['X-CSRF-TOKEN']);

        return $this->client->getAsync($url, [
            'headers' => $getHeaders,
            'handler' => $this->handler
        ])->then(
            function ($response) {return $this->responseHandler($response);},
            function ($exception) {return $this->exceptionHandler($exception);}
        )->wait();
    }


    public function post($url, $type, $parameter)
    {
        $this->setHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'TE' => 'Trailers',
            'Origin' => 'https://www.sikayetvar.com',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
        ]);

        $data = [
            'headers' => $this->headers,
            'handler' => $this->handler
        ];

        if (is_array($parameter) && count($parameter) > 0)
        {
            $data[$type] = $parameter;
        }

        return $this->client->postAsync($url,$data)->then(
            function ($response) {return $this->responseHandler($response);},
            function ($exception) {return $this->exceptionHandler($exception);}
        )->wait();
    }

    public function responseHandler($response)
    {
        info($response->getBody()->__toString());
        return collect([
            "content" => $response->getBody()->__toString()
        ]);
    }

    public function exceptionHandler($exception)
    {
        if ($exception instanceof ConnectException)
        {
            info("Bağlantı Çok Yavaş Timeout Süresi Doldu !");
            return collect([
                "exception" => "Bağlantı Çok Yavaş Timeout Süresi Doldu !",
            ]);
        }
        elseif ($exception instanceof \InvalidArgumentException)
        {
            info($exception->getMessage());
            return collect([
                "exception" => $exception->getMessage(),
            ]);
        }
        elseif ($exception instanceof ErrorException)
        {
            info($exception->getMessage());
            return collect([
                "exception" => $exception->getMessage(),
            ]);
        }
        else
        {
            return collect([
                "exception" => !is_null($exception->getResponse()) ? $exception->getResponse()->getBody()->getContents() : 'Proxyden Cevap Yok !',
            ]);
        }
        //return new Exception($exception->getMessage());
    }

}