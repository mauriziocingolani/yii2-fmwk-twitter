<?php
/* @var $tweet mauriziocingolani\yii2fmwktwitter\TwitterTweet */
/**
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version 1.1
 */
?>

<!-- 
*Codice HTML per la visualizzazione del tweet.
*La classe .tweet applicata alla <div> esterna permette di applicare stii ai tag interni. 
-->
<div class="tweet">
    <header>
        <i class="fa fa-twitter"></i>
        <span><?= date('d-m-Y', strtotime($tweet->created)); ?></span>
    </header>
    <p><?= $tweet->text; ?></p>
</div>  