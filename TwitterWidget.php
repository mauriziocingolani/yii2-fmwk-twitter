<?php

namespace mauriziocingolani\yii2fmwktwitter;

/**
 * @property TwitterTweet $tweet
 */
class TwitterWidget extends \yii\base\Widget {

    public $tweet;

    public function init() {
        parent::init();
    }

    public function run() {
        return $this->render('TwitterWidgetView', ['tweet' => $this->tweet]);
    }

}
