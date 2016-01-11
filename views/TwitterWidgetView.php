<?php
/* @var $tweet mauriziocingolani\yii2fmwktwitter\TwitterTweet */
?>

<!-- 
*Codice HTML per la visualizzazione del tweet.
*La classe .tweet applicata alla <div> esterna permette di applicare stii ai tag interni. 
-->
<div class="tweet">
    <header>
        <i class="fa fa-twitter"></i>
        <span title="<?= $tweet->idStr; ?>"><?= date('d-m-Y', strtotime($tweet->created)); ?></span>
    </header>
    <p><?= $tweet->text; ?></p>
</div>  