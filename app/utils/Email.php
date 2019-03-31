<?php
namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
  public static function sendMail($emailData)
  {
    $mail = new PHPMailer(true);

    $message_html = file_get_contents($emailData['path_template']);

    $message_html = str_replace('%APP_NAME%', APP_NAME, $message_html);

    if(count($emailData['data']) > 0){
      foreach ($emailData['data'] as $key => $value) {
        $message_html = str_replace($key, $value, $message_html);
      }
    }

    try {
      //Server settings
      $mail->SMTPDebug = 0;                                       // Enable verbose debug output
      $mail->isSMTP();                                            // Set mailer to use SMTP
      $mail->Host       = EMAIL_HOST;  // Specify main and backup SMTP servers
      $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
      $mail->Username   = EMAIL_USER;                     // SMTP username
      $mail->Password   = EMAIL_PASS;                               // SMTP password
      $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
      $mail->Port       = EMAIL_PORT;                                    // TCP port to connect to

      //Recipients
      $mail->setFrom(EMAIL_USER, APP_NAME);

      if(count($emailData['to']) > 0){
        foreach ($emailData['to'] as $to) {
          $mail->addAddress($to);
        }
      }

      if(isset($emailData['reply_to'])){
        $mail->addReplyTo($emailData['reply_to']);
      }

      if(count($emailData['cc']) > 0){
        foreach ($emailData['cc'] as $cc) {
          $mail->addCC($cc);
        }
      }

      if(count($emailData['bcc']) > 0){
        foreach ($emailData['bcc'] as $bcc) {
          $mail->addBCC($bcc);
        }
      }

      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = $emailData['subject'];
      $mail->Body    = $message_html;
      $mail->AltBody = strip_tags($message_html);

      $result = $mail->send();
      return ['response' => 'ok', 'message' => 'Message has been sent', 'result' => $result];

    } catch (\Exception $e) {
      return ['response' => 'error', 'message' => $mail->ErrorInfo];
    }
  }
}