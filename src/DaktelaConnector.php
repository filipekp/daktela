<?php
  
  namespace filipekp\daktela;
  
  use PF\helpers\MyArray;
  use Tracy\Debugger;
  use Tracy\ILogger;
  
  /**
   * Třída DaktelaConnector.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   21.09.2020
   */
  class DaktelaConnector
  {
    const API_VERSION = 'v6';
    const API_URL_SCHEMA = 'https://%s/api/%s/%s.json?%s';
    
    const REQTYPE_GET   = 'GET';
    const REQTYPE_POST  = 'POST';
    const REQTYPE_PUT   = 'PUT';
    const REQTYPE_DELETE = 'DELETE';
    
    /** @var string základní URL daktely ústředny např.: mydaktela.daktela.com */
    private static $_BASE_URL_MY_DAKTELA;
    
    /** @var string přiřazeno od daktely */
    private static $_ACCESS_TOKEN;
    
    /** @var DaktelaConnector */
    private static $instance = NULL;
    
    private $lastHttpStatus = NULL;
    
    private function __construct($baseUrl, $accessToken = '') {
      self::$_BASE_URL_MY_DAKTELA = $baseUrl;
      self::$_ACCESS_TOKEN = $accessToken;
    }
    
    /**
     * Vytvoří konektor pro komunikaci s DAKTELA api.
     *
     * @param $baseUrl
     * @param $accessToken
     *
     * @return DaktelaConnector
     */
    public static function init($baseUrl, $accessToken = '') {
      if (is_null(self::$instance)) {
        self::$instance = new self($baseUrl, $accessToken);
      }
      
      global $daktelaConnector;
      $daktelaConnector = self::$instance;
      
      return self::$instance;
    }
    
    /**
     * @param string $endpoint
     * @param array  $queryParams
     * @param array  $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute(string $endpoint, array $queryParams = [], array $data = [], $requestType = "GET") {
      if (!defined(__CLASS__ . '::REQTYPE_' . strtoupper($requestType))) {
        throw new \Exception('`' . $requestType . '` is not correct request type. Use request type from ' . __CLASS__ . '::REQTYPE_...');
      }
      
      $ch = curl_init();
      if (self::$_ACCESS_TOKEN) {
        $queryParams = array_merge($queryParams, [
          "accessToken" => self::$_ACCESS_TOKEN
        ]);
      }
      
      $unmaskedUrl = vsprintf(self::API_URL_SCHEMA, [
        self::$_BASE_URL_MY_DAKTELA,
        self::API_VERSION,
        $endpoint,
        http_build_query($queryParams),
      ]);
      
      curl_setopt($ch, CURLOPT_URL, $unmaskedUrl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      if (in_array($requestType, [self::REQTYPE_PUT, self::REQTYPE_POST])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
      }
      $response = curl_exec($ch);
      $this->lastHttpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      
      if ($this->lastHttpStatus >= 500) {
        throw new \Exception('Internal server error.', $this->lastHttpStatus);
      }
      
      if ($this->lastHttpStatus == 401) {
        throw new \Exception('Unauthorized!', $this->lastHttpStatus);
      }
      
      if (($errNo = curl_errno($ch))) {
        throw new \Exception(curl_error($ch), $errNo);
      }
      
      curl_close($ch);
      
      if ($response) {
        $decodedResponse = json_decode($response);
        $myDecodedResponse = (array)$decodedResponse;
        $decodedResponseArr = MyArray::init($myDecodedResponse);
        
        // vyhození vyjímky v případě chyby.
        if (($errors = $decodedResponseArr->item('error'))) {
          Debugger::log('Error from call API Daktela', ILogger::ERROR);
          Debugger::log('URL: ' . $unmaskedUrl, ILogger::ERROR);
          $firstErr = '';
          foreach ($errors as $errName => $error) {
            $err = json_encode([$errName => $error], JSON_UNESCAPED_UNICODE);
            Debugger::log($err, ILogger::ERROR);
            if (!$firstErr) {
              $firstErr = $err;
            }
          }
          
          throw new \Error($firstErr, 500);
        }
        
        return $decodedResponse->result;
      }
      
      return $this->lastHttpStatus;
    }
    
    /**
     * @return integer|null
     */
    public function getLastHttpStatus() {
      return $this->lastHttpStatus;
    }
    
    /**
     * Vrátí access token pro požadavky.
     *
     * @param $login
     * @param $password
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getToken($login, $password) {
      $result = $this->execute('login', [], [
        'username'   => $login,
        'password'   => $password,
        'only_token' => 1
      ], self::REQTYPE_POST);
      
      self::$_ACCESS_TOKEN = $result;
      
      return self::$_ACCESS_TOKEN;
    }
  }