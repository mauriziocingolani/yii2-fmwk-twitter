<?php

namespace mauriziocingolani\yii2fmwktwitter;

use Yii;
use mauriziocingolani\yii2fmwkphp\Html;

/**
 * Rappresenta un tweet con le sue proprietà.
 * @property text $text
 * @property text $idStr
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version 1.0.3
 */
class TwitterTweet extends \yii\base\Object {

    /** ID del tweet */
    private $_id_str;

    /** Data e ora di creazione */
    private $_created;

    /** Testo del tweet */
    private $_text;

    /** Lista degli hashtag contenuti nel tweet */
    private $_hashtags;

    /** Lista dei link contenuti nel tweet */
    private $_urls;

    /**
     * Estrae dall'oggetto restituito dal metodo user_timeline dell'API di Twitter i dati del tweet (testo, hastags, ...).
     * @param mixed $data Oggetto con i dati del tweet
     */
    public function __construct($data) {
        $this->_id_str = $data->id_str;
        $this->_created = strtotime($data->created_at);
        $this->_text = $data->text;
        $this->_hashtags = $data->entities->hashtags;
        $this->_urls = $data->entities->urls;
    }

    /**
     * Restituisce l'ID del tweet.
     * @return string ID del tweet
     */
    public function getIdStr() {
        return $this->_id_str;
    }

    /**
     * Restituisce il testo del tweet applicando i tag html agli hashtags (tag <span> con classe .hashtag) e
     * ai link (tag <a>).
     * @return string Testo del tweet
     */
    public function getText() {
        $text = trim(str_replace('#' . Yii::$app->twitter->hashtag, '<span class="hashtag">#' . Yii::$app->twitter->hashtag . '</span>', $this->_text));
        if (is_array($this->_urls)) :
            foreach ($this->_urls as $url) :
                $text = str_replace($url->url, Html::a($url->display_url, $url->expanded_url, array('target' => 'blank')), $text);
            endforeach;
        endif;
        return $text;
    }

    /**
     * Restituisce la data di creazione del tweet, secondo il formato passato come parametro.
     * @param string $format Formato della data ('d-m-Y' di default)
     * @return string Data di creazione delll tweet
     */
    public function getCreated($format = 'd-m-Y') {
        return date($format, $this->_created);
    }

}
