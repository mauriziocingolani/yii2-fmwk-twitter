<?php

namespace mauriziocingolani\yii2fmwktwitter;

use Yii;
use yii\db\ActiveRecord;
use mauriziocingolani\yii2fmwkphp\Html;

/**
 * Rappresenta un tweet contenuto nella tabella YiiTweets.
 * 
 * @property integer $id Chiave primaria
 * @property string $id_str ID del tweet
 * @property string $created Date e ora di creazione
 * @property string $text Testo del tweet
 * 
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version 1.1
 */
class TwitterTweet extends ActiveRecord {

    public static function tableName() {
        return 'YiiTweets';
    }

    /* Relazioni */
    /* Eventi */
    /* Metodi */
    /* Getters-Setters */
    /* Metodi statici */

    /**
     * Partendo dall'oggetto che la Api Twitter restituisce per ogni tweet, verifica in base alla proprietà <code>id_str</code>
     * se il tweet è già presente nel database. In caso affermativo restituisce true, altrimenti inserisce in nuovo record
     * nel database e lo restituisce.
     * @param mixed $data Oggetto del tweet restituito dalla Api Twitter
     * @return mixed True se il tweet è già presente nel database, altrimenti nuovo record creato
     */
    public static function CreateFromObj($data) {
        $tweet = self::find()->where('id_str=:idstr', [':idstr' => $data->id_str])->one();
        if (!$tweet) : # Nuovo tweet: lo inserisco nel database
            $tweet = new TwitterTweet;
            $tweet->id_str = $data->id_str;
            $tweet->created = date('Y-m-d H:i:s', strtotime($data->created_at));
            $text = trim(str_replace('#' . Yii::$app->twitter->hashtag, '<span class="hashtag">#' . Yii::$app->twitter->hashtag . '</span>', $data->text));
            $urls = $data->entities->urls;
            if (is_array($urls)) :
                foreach ($urls as $url) :
                    $text = str_replace($url->url, Html::a($url->display_url, $url->expanded_url, array('target' => 'blank')), $text);
                endforeach;
            endif;
            $tweet->text = $text;
            $tweet->save();
            $tweet->refresh();
            return $tweet;
        endif;
        return true;
    }

}
