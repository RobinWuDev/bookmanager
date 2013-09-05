<?php
require_once "../extral/PHPMailer/class.phpmailer.php";
/**
 * 邮件类
 */
class Email {

    public static function sendExpiredEmail( $personName, $personEmail, $bookName ) {
        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Host = 'smtp.exmail.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wjh@suncco.com';
        $mail->Password = 'wjh460490151';
        $mail->SMTPSecure = 'http';

        $mail->From = 'wjh@suncco.com';
        $mail->FromName = '图书管理员';
        $mail->AddAddress( $personEmail, $personName );

        $mail->WordWrap = 50;
        $mail->IsHTML( true );

        $mail->Subject = 'Here is the subject';
        $mail->Body    = $bookName;
        $mail->AltBody = $bookName;

        if ( !$mail->Send() ) {
            $file = fopen( "test.txt", "w" );
            echo fwrite( $file, 'Message could not be sent.' );
            echo fwrite( $file, 'Mailer Error: ' . $mail->ErrorInfo );
            fclose( $file );
            exit;
        }

        $file = fopen( "test.txt", "w" );
        echo fwrite( $file, 'Send Sunccess' );
        fclose( $file );
    }
}
?>
