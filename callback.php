<?php
ini_set('display_errors', 0);
define( 'CONSUMER_KEY', '' );//twitter APIキー
define( 'CONSUMER_SECRET', '' ); //Twitter APIsecretキー
session_start();
if(isset($_GET['denied'])){
    header("Location: .");
    exit;
}
require_once 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
function send_to_slack($message,$webhook_url) {
    $options = array(
      'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($message),
      )
    );
    $response = file_get_contents($webhook_url, false, stream_context_create($options));
    return $response === 'ok'; 
  }
try{
    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
    if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
        die( 'Error!' );
    }
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
    $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $connection->get("account/verify_credentials",['include_email'=> 'true']);
    $user=(array)$user;
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    if($_SESSION['job']==1){
        $jobs="希望なし";
    }else if($_SESSION['job']==2){
        $jobs="技術部";
    }else if($_SESSION['job']==3){
        $jobs="運営部";
    }else if($_SESSION['job']==4){
        $jobs="動画編集部";
    }else if($_SESSION['job']==5){
        $jobs="広報部";
    }else if($_SESSION['job']==6){
        $jobs="マインクラフト部";
    }
    // メールを送信する
    $to = ""; //送信先メールアドレス
    $subject = $_SESSION['name']."さんからメンバー申請が届きました";
    $message = "twitter ID:".$user['id']."\r\nTwitter Name:".$user['name']."\r\nTwitter screen_name:".$user['screen_name']."\r\nメール:".$_SESSION['email']."\r\nお名前:".$_SESSION['name']."\r\n希望部署:".$jobs."\r\n自己PR:".$_SESSION['pr']."\r\n質問:".$_SESSION['question'];
    $headers = "From: info@example.com"; //サイトメール等を指定してください
    //mb_send_mail($to, $subject, $message, $headers);

    //discordにwebhookを送る
      $message = array(
        'username' => $_SESSION['name']."さんからメンバー申請が届きました", 
        'content' => $message
      );
      //send_to_slack($message,"webhook URL"); //処理を実行

    //$message←この変数に申請内容を入れてます
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
    <title>メンバー申請フォーム</title>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <!-- 製作-むーんず(twitter:@moons14_) -->
    </head>
    <body>
    <br>
    <div class="container" id="form">
    <h3 class="text-center">メンバー申請フォーム</h3>
    <br><br>
    <h2 class="text-center">完了</h2>
    <p>ご申請ありがとうございます</p>
    <br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> 
    </body>
    </html>
    <?php
}catch ( Exception $ex ) {
    header("Location: .");
    exit;
}
