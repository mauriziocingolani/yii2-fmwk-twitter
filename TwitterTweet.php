<?php

namespace mauriziocingolani\yii2fmwktwitter;

use Yii;
use mauriziocingolani\yii2fmwkphp\Html;

/**
 * @property text $text
 */
class TwitterTweet extends \yii\base\Object {

    private $_created;
    private $_text;
    private $_hashtags;
    private $_urls;

    public function __construct($data) {
        $this->_created = strtotime($data->created_at);
        $this->_text = $data->text;
        $this->_hashtags = $data->entities->hashtags;
        $this->_urls = $data->entities->urls;
    }

    public function getText() {
        $text = trim(str_replace('#' . Yii::$app->twitter->hashtag, '<span class="hashtag">#' . Yii::$app->twitter->hashtag . '</span>', $this->_text));
        if (is_array($this->_urls)) :
            foreach ($this->_urls as $url) :
                $text = str_replace($url->url, Html::a($url->display_url, $url->expanded_url, array('target' => 'blank')), $text);
            endforeach;
        endif;
        return $text;
    }

    public function getCreated($format = 'd-m-Y') {
        return date($format, $this->_created);
    }

}
