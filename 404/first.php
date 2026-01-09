<?php
include "./observer/interface.php";
include "./observer/NewsAgency.php";

include "./observer/subscriber/EmailSubscriber.php";
include "./observer/subscriber/ManuSubscriber.php";
function Agency(){
    $news = new NewsAgency();
    $news->attach(new EmailSubscriber());
    $news->attach(new ManuSubscriber());
    return $news;
}
//Agency()->setNews("this is news happended here");
?>
