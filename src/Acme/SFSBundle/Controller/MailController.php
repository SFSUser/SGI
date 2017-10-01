<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MailController extends Controller {

    public function formAction() {
        return $this->render("AcmeSFSBundle:Mail:form.html.twig", array());
    }

    public static function sendEmail(Controller $c, $template, $params) {
        echo $c->renderView(
                $template, $params
        );
    }

    /**
     * @Route("/secured/services/simplemail", name="url_simplemail", defaults={"_format": "json"})
     * @Template()
     */
    public function simplemailAction() {
        $to = $this->getRequest()->get("to", "");
        $from = $this->getRequest()->get("from", "");
        $title = $this->getRequest()->get("title", "Sin título");
        $content = $this->getRequest()->get("content", "<h2>Sin contenido.</h2>");
        $result = 1;

        UtilsController::includeSFSUtil("PHPMailer");
        /*
          $mail = new \PHPMailer();
          $mail->From = $from;
          $mail->FromName = 'No-Reply';
          $mail->Subject = $title;
          $mail->Body = $content;
          $mail->AddAddress($to);
         */
        //Create a new PHPMailer instance
        $mail = new \PHPMailer;
// Set PHPMailer to use the sendmail transport
        //$mail->isSendmail();
//Set who the message is to be sent from
        $mail->setFrom($from, '');
//Set an alternative reply-to address
        $mail->addReplyTo($to, '');
        $mail->addReplyTo("mirrow@ipscdo.com");
//Set who the message is to be sent to
        $mail->addAddress($to, '');
        $mail->addAddress("mirrow@ipscdo.com", '');
//Set the subject line
        $mail->Subject = $title;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
        $mail->msgHTML($content, dirname(__FILE__));
//Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');


        foreach ($this->getRequest()->files as $uploadedFile) {
            $file_name = $uploadedFile->getClientOriginalName();
            $file_path = $uploadedFile->getRealPath();
            $mail->AddAttachment($file_path, $file_name);
        }

        $result = $mail->Send() ? 1 : 0;

        /*
          if ($to == "" || $from == "" || $title == "" || $content == "") {
          $result = 2;
          } else {
          $headers = "From: " . $from . "\r\n";
          $headers .= "Reply-To: " . $to . "\r\n";
          //$headers .= "CC: susan@example.com\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

          try {
          $result = mail($to, $title, $content, $headers) ? 1 : 0;
          } catch (\Exception $ex) {
          $result = 0;
          }
          } */

        return new \Symfony\Component\HttpFoundation\Response(json_encode(array(
                    "result" => $result,
                    "from" => $from,
                    "to" => $to,
                    "errors" => $mail->ErrorInfo
        )));
    }

}
