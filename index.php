<?php
$err="";
//recapchaのAPIキー、APIsecretキー
$site_key="";
$secret_key="";
ini_set('display_errors', 0);
session_start();
define( 'CONSUMER_KEY', '' );//twitter APIキー
define( 'CONSUMER_SECRET', '' ); //Twitter APIsecretキー
define( 'OAUTH_CALLBACK', '' ); //コールバックURL(当然相対パスはダメ)
require_once 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
if(isset($_POST['go'])){
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret_key."&response=".$_POST["recaptchaResponse"]);
        $reCAPTCHA = json_decode($verifyResponse);
        if ($reCAPTCHA->success)
        {
            if(!isset($_POST['email'])||$_POST['email']==""||mb_strlen($_POST['email'])>150){
                $err="メールアドレスを入力してください。もしくは150字以内のメールアドレスをご利用ください";
                goto fi;
            }else{
                if(!isset($_POST['name'])||$_POST['name']==""||mb_strlen($_POST['name'])>100){
                    $err="お名前を入力してください。もしくは100字以内で入力してください";
                    goto fi;
                }else{
                    if(!isset($_POST['job'])||$_POST['job']==""){
                        $err="希望部署を正しく選択してください";
                        goto fi;
                    }else{
                        if(!isset($_POST['pr'])||$_POST['pr']==""||mb_strlen($_POST['pr'])>10000||mb_strlen($_POST['pr'])<100){
                            $err="自己prを100字から10000字までで入力してください。";
                            goto fi;
                        }else{
                            if($_POST['job']!=1&&$_POST['job']!=2&&$_POST['job']!=3&&$_POST['job']!=4&&$_POST['job']!=5&&$_POST['job']!=6){
                                $err="希望部署を正しく選択してください";
                                 goto fi;
                            }else{
                                $_SESSION['email']=$_POST['email'];
                                $_SESSION['name']=$_POST['name'];
                                $_SESSION['job']=$_POST['job'];
                                $_SESSION['pr']=$_POST['pr'];
                                $_SESSION['question']=$_POST['question'];
                                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
                                $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
                                $_SESSION['oauth_token'] = $request_token['oauth_token'];
                                $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
                                $url = $connection->url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));
                                header( 'location: '. $url );
                            }
                        }
                    }
                }
            }
        }else{
            $err="recaptchaエラー";
                                 goto fi;
        }
    
}
fi:
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
  <title>メンバー申請フォーム</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo$site_key;?>"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute("<?php echo$site_key;?>", {action: "sent"}).then(function(token) {
            var recaptchaResponse = document.getElementById("recaptchaResponse");
            recaptchaResponse.value = token;
            });
        });
    </script>
    <!-- 製作-むーんず(twitter:@moons14_) -->
  </head>
  <body>
  <br>
  <div class="container" id="form">
  <h3 class="text-center">メンバー申請フォーム</h3>
  <p class="text-center">送信前にTwitter認証が必須となります。</p>
  <h5 class="text-center" style="color:red"><?php echo $err?></h5>
  <br>
  <form action="." method="post">
  <input type="hidden" name="recaptchaResponse" id="recaptchaResponse">
  <input class="form-control" placeholder="メールアドレス(必須)" id="email" name="email" value="<?php echo $_POST['email']?>" required>
  <br>
  <input class="form-control" placeholder="お名前(必須)" id="name" name="name" value="<?php echo $_POST['name']?>" required>
  <br>
    希望部署
    <select class="custom-select form-control" aria-label="Default select example" id="job" name="job" required>
        <option value="1" selected>希望なし</option>
        <option value="2">技術部</option>
        <option value="3">運営部</option>
        <option value="4">動画編集部</option>
        <option value="5">広報部</option>
        <option value="6">マインクラフト部</option>
    </select>
  <br>
  <textarea class="form-control" id="pr" placeholder="自己PR (最低100文字)" name="pr" minlength="100" rows="3" required><?php echo $_POST['pr']?></textarea>
  <br>
  <textarea class="form-control" placeholder="ご質問" rows="1" id="question" name="question"><?php echo $_POST['question']?></textarea>
  <br>
  <button type="button" class="btn btn-success" style="width:100%;" onclick="next()">次へ</button>
  <br>
  <input type="submit" id="post_btn" name="go" style="display:none;">
  </form>
  </div>
  <div class="container" id="view" style="display:none;">
  <br>
  <h3 class="text-center">ご確認ください</h3>
  <br>
  <p>メールアドレス</p>
  <div id="email_view" style="word-break : break-all;"></div>
  <br>
  <p>お名前</p>
  <div id="name_view" style="word-break : break-all;"></div>
  <br>
  <p>希望部署</p>
  <div id="job_view" style="word-break : break-all;"></div>
  <br>
  <p>自己PR</p>
  <div id="pr_view" style="word-break : break-all;"></div>
  <br>
  <p>ご質問</p>
  <div id="question_view"></div>
  <p class="text-center" onclick="back()">前に戻る</p>
  <button type="button" class="btn btn-info" style="width:100%;" id="ok" onclick="ok()">確認</button>
  <button type="button" class="btn btn-outline-info" style="width:100%;display:none;" id="post" onclick="post()">Twitter認証へ</button>
  <br><br>
  </div>
    <!-- page content -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src= "https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> 
<script>
function next(){
    if($("#pr").val().length>=100){
        if($("#email").val()){
            if($("#name").val()){
                if($("#job").val()){
                    $('#form').fadeOut();
                    $("#email_view").text($("#email").val());
                    $("#name_view").text($("#name").val());
                    if($("#job").val()==1){
                        $("#job_view").text("希望なし");
                    }else if($("#job").val()==2){
                        $("#job_view").text("技術部");
                    }else if($("#job").val()==3){
                        $("#job_view").text("運営部");
                    }else if($("#job").val()==4){
                        $("#job_view").text("動画編集部");
                    }else if($("#job").val()==5){
                        $("#job_view").text("広報部");
                    }else if($("#job").val()==6){
                        $("#job_view").text("マインクラフト部");
                    }
                    $("#pr_view").text($("#pr").val());
                    $("#question_view").text($("#question").val());
                    $('#view').slideDown();
                }else{
                swal( 'エラー', '希望部署を選択してください', 'error' );
                }
            }else{
            swal( 'エラー', 'お名前を入力してください', 'error' );
            }
        }else{
        swal( 'エラー', 'メールアドレスを入力してください', 'error' );
        }
    }else{
        swal( 'エラー', '自己PRは100字以上入力してください', 'error' );
    }
}
function back(){
    $('#view').fadeOut();
    $('#form').slideDown();
}
function ok(){
    $('#ok').fadeOut();
    $('#post').slideDown();
}
function post(){
    $("#post_btn").click();
}
</script>
  </body>
</html>
