<?php

namespace mauriziocingolani\yii2fmwktwitter;

/**
 * Widget Yii2 per visualizzare un tweet.
 * @property TwitterTweet $tweet
 */
class TwitterWidget extends \yii\base\Widget {

    /** Tweet da visualizzare */
    public $tweet;

    public function init() {
        parent::init();
    }

    public function run() {
        return $this->render('TwitterWidgetView', ['tweet' => $this->tweet]);
    }

}
