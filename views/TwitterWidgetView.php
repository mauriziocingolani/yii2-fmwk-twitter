<?php
/* @var $tweet mauriziocingolani\yii2fmwktwitter\TwitterTweet */
?>

<div class="tweet">
    <header>
        <i class="fa fa-twitter"></i>
        <span><?= date('d-m-Y', strtotime($tweet->created)); ?></span>
    </header>
    <p><?= $tweet->text; ?></p>
</div>  