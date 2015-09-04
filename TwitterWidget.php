<?php

namespace mauriziocingolani\yii2fmwktwitter;

/**
 * Widget Yii2 per visualizzare un tweet.
 * @property TwitterTweet $tweet
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version 1.0
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
