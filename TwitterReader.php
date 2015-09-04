<?php

namespace mauriziocingolani\yii2fmwktwitter;

use Yii;

/**
 * Tramite questo oggetto è possibile iterare sulla lista dei tweets.
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version 1.0.1
 */
class TwitterReader extends \yii\base\Object implements \Iterator {

    private $postfields;
    private $getfield;
    protected $oauth;
    public $url;
    private $_tweets;
    private $_tweets_index;

    /**
     * Costruisce una nuova istanza e recupera la lista dei tweet, eventualmente limita
     * al numero passato come parametro.
     * @param int $limit Numero massimo di tweet da considerare
     * @throws InvalidConfigException
     */
    public function __construct($limit = null) {
        parent::__construct();
        if (!Yii::$app->twitter->screenName || !Yii::$app->twitter->hashtag)
            throw new InvalidConfigException('Componente Twitter: parametri di configurazione mancanti.');
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?count=3200&screen_name=' . Yii::$app->twitter->screenName;
        $requestMethod = 'GET';
        $tweets = json_decode($this->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest());
        if (isset($limit) && (int) $limit > 0) :
            $limit = (int) $limit;
        else :
            $limit = 3200;
        endif;
        $i = 0;
        foreach ($tweets as $tw) :
            foreach ($tw->entities->hashtags as $ht) :
                if ($ht->text === Yii::$app->twitter->hashtag) :
                    $this->_tweets[] = new TwitterTweet($tw);
                    $i++;
                    break;
                endif;
            endforeach;
            if ($i == $limit)
                break;
        endforeach;
        $this->_tweets_index = 0;
    }

    /* Implementazione interfaccia Iterator */

    public function current() {
        return $this->_tweets[$this->_tweets_index];
    }

    public function key() {
        return $this->_tweets_index;
    }

    public function next() {
        ++$this->_tweets_index;
    }

    public function rewind() {
        $this->_tweets_index = 0;
    }

    public function valid() {
        return $this->_tweets_index < count($this->_tweets);
    }

    /* Metodi */

    /**
     * Restituisce il numero attuale di tweets
     * @return int Numero di tweets
     */
    public function getCount() {
        return count($this->_tweets);
    }

    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     * @param string $string Get key and value pairs as string
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setGetfield($string) {
        if (!is_null($this->getPostfields())) {
            throw new Exception('You can only choose get OR post fields.');
        }
        $search = array('#', ',', '+', ':');
        $replace = array('%23', '%2C', '%2B', '%3A');
        $string = str_replace($search, $replace, $string);
        $this->getfield = $string;
        return $this;
    }

    /**
     * Get getfield string (simple getter)
     * @return string $this->getfields
     */
    public function getGetfield() {
        return $this->getfield;
    }

    /**
     * Get postfields array (simple getter)
     * @return array $this->postfields
     */
    public function getPostfields() {
        return $this->postfields;
    }

    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    private function buildOauth($url, $requestMethod) {
        if (!in_array(strtolower($requestMethod), array('post', 'get')))
            throw new Exception('Request method must be either POST or GET');
        $consumer_key = Yii::$app->twitter->consumer_key;
        $consumer_secret = Yii::$app->twitter->consumer_secret;
        $oauth_access_token = Yii::$app->twitter->oauth_access_token;
        $oauth_access_token_secret = Yii::$app->twitter->oauth_access_token_secret;
        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        $getfield = $this->getGetfield();
        if (!is_null($getfield)) :
            $getfields = str_replace('?', '', explode('&', $getfield));
            foreach ($getfields as $g) :
                $split = explode('=', $g);
                $oauth[$split[0]] = $split[1];
            endforeach;
        endif;
        $base_info = $this->buildBaseString($url, $requestMethod, $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
        $this->url = $url;
        $this->oauth = $oauth;
        return $this;
    }

    /**
     * Private method to generate the base string used by cURL
     * @param string $baseURI
     * @param string $method
     * @param array $params
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params) {
        $return = array();
        ksort($params);
        foreach ($params as $key => $value)
            $return[] = "$key=" . $value;
        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
    }

    /**
     * Perform the actual data retrieval from the API
     * @param boolean $return If true, returns data.
     * @return string json If $return param is true, returns json data.
     */
    private function performRequest($return = true) {
        if (!is_bool($return))
            throw new Exception('performRequest parameter must be true or false');
        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');
        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();
        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        );
        if (!is_null($postfields)) :
            $options[CURLOPT_POSTFIELDS] = $postfields;
        else :
            if ($getfield !== '') :
                $options[CURLOPT_URL] .= $getfield;
            endif;
        endif;
        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);
        if ($return)
            return $json;
    }

    /**
     * Private method to generate authorization header used by cURL
     * @param array $oauth Array of oauth data generated by buildOauth()
     * @return string $return Header used by cURL for request
     */
    private function buildAuthorizationHeader($oauth) {
        $return = 'Authorization: OAuth ';
        $values = array();
        foreach ($oauth as $key => $value) :
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        endforeach;
        return $return . implode(', ', $values);
    }

}
