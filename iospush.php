<?php

date_default_timezone_set( 'PRC' );
error_reporting( -1 );
if ( $_SERVER['REQUEST_METHOD']=='POST' ) {

    $payload  = $_POST['payload'];
    $token = $_POST['token'];
    $cert  = $_FILES["myfile"]["tmp_name"][0];

    // echo $text.$token.$cert.$key;

    $apnsHost = 'gateway.sandbox.push.apple.com';
    $apnsPort = 2195;
    // $apnsCert = 'apns-dev.pem';
    $streamContext = stream_context_create();
    stream_context_set_option( $streamContext, 'ssl', 'local_cert', $cert );
    $apns = stream_socket_client( 'ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2,
        STREAM_CLIENT_CONNECT, $streamContext );
    $apnsMessage = chr( 0 ) . chr( 0 ) . chr( 32 ) . pack( 'H*', str_replace( ' ', '', $token ) ) . chr( 0 ) .
        chr( strlen( $payload ) ) . $payload;
    $result = fwrite( $apns, $apnsMessage );
    if ($result) {
        echo "发送成功";
    } else {
        echo "发送失败";
    }
    @socket_close( $apns );
    @fclose( $apns );

}
?>

<html>
<head>
    <title>尚科iOS消息推送测试平台</title>
    <meta http-equiv=Content-Type content="text/html;charset=utf-8">
</head>
<body>
    <form action="iospush.php" method="post" enctype="multipart/form-data">
        payload:
        <textarea name="payload" style="width:100%;height:300px;">{
    "aps" : {
        "alert" : {
            "body" : "Bob wants to play poker",
            "action-loc-key" : "PLAY"
        },
        "badge" : 5,
    },
    "acme1" : "bar",
    "acme2" : [ "bang",  "whiz" ]
}</textarea>
        <br/>
        token:
        <input type="text" name="token" value='4ed4e735ab05530eba4e9e023af7dc186b5fc9d0b82717216f3c0e8eba6c0df5' style="width:300px;"/>
        <br/>
        证书:
        <input type="file" name="myfile[]" value="<?php echo $cert;?>"/>
        <br/>
        <button type="submit" >提交</button>
    </form>
    <p>生成证书方法:openssl pkcs12 -in cert.p12 -out apple_push_notification_production.pem -nodes -clcerts</p>
</body>
</html>
