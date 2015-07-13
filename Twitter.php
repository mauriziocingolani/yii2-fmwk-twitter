<?php

namespace mauriziocingolani\yii2fmwktwitter;

use yii\base\InvalidConfigException;

/**
 * Componente per la visualizzazione dei tweet relativi all'applicazione.
 * Richiede l'inizializzazione in fase di configurazione dei seguenti parametri:
 * <ul>
 * <li>$oauth_access_token: Access Token</li>
 * <li>$$oauth_access_token_secret: Access Token Secret</li>
 * <li>$consumer_key: Consumer Key (API key)</li>
 * <li>$consumer_secret: Consumer Secret (API secret)</li>
 * <li>$hashtag: hastag relativo all'applicazione</li>
 * <li>$screenName: account Twitter autore dei tweets</li>
 * </ul>
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0
 */
class Twitter extends \yii\base\Component {

    public $oauth_access_token;
    public $oauth_access_token_secret;
    public $consumer_key;
    public $consumer_secret;
    public $hashtag;
    public $screenName;

    /**
     * Inizializza il componente e verifica ch ei parametri siano stati impostati.
     * @throws InvalidConfigException
     */
    public function init() {
        parent::init();
        if (!$this->oauth_access_token ||
                !$this->oauth_access_token_secret ||
                !$this->consumer_key ||
                !$this->consumer_secret ||
                !$this->hashtag ||
                !$this->screenName)
            throw new InvalidConfigException('Componente Twitter: parametri di configurazione mancanti.');
    }

    /**
     * Restituisce la lista dei tweet (eventualmente limitata al numero indicato)
     * @param integer $limit Numero di tweet che devono essere restituiti
     * @return \mauriziocingolani\yii2fmwktwitter\TwitterReader Lista dei tweet
     */
    public function tweets($limit = null) {
        return new TwitterReader($this->screenName, $limit);
    }

}
