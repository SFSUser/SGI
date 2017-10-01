<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class UploaderController extends Controller {

    /**
     * @Route("/uploader/download/{id}", name="uploacer_download_file")
     */
    public function redirectuploadAction($id) {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");
        
        $element = $repo->find($id);
        
        if($element === null){
            return new \Symfony\Component\HttpFoundation\Response("<h1>Element not found!</h1>");
        }
        
        $path = $element->getFileLocation();
        
        if (is_readable($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $element->getOriginal());
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        } else {
            return new \Symfony\Component\HttpFoundation\Response("<h1>File not found!</h1>");
        }
    }

    static function create_zip($files = array(), $destination = '', $overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        //vars
        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            foreach ($files as $file) {
                //make sure the file exists
                if (file_exists($file)) {
                    $valid_files[] = $file;
                } else {
                    echo "<h1>Error 1: $file</h1>";
                }
            }
        }
        //if we have good files...
        if (count($valid_files)) {
            //create the archive
            $zip = new \ZipArchive();
            //OVERWRITE
            if ($zip->open($destination, $overwrite ? \ZipArchive::OVERWRITE : \ZipArchive::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach ($valid_files as $file) {
                $files_dest = basename($file, ".file");
                $zip->addFile($file, $files_dest);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            //close the zip -- done!
            $zip->close();

            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

    /**
     * @Route("/service/uploader/zip", name="url_uploaderzip")
     */
    public function zipAction() {
        $identifier = $this->getRequest()->get("identifier");
        $files = $this->getRequest()->get("files", array());

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository("AcmeSFSBundle:SFSUploads");

        $files_list = $repo->findBy(array(
            "identifier" => $identifier
        ));

        $selected_files = array();
        foreach ($files_list as $x) {
            foreach ($files as $y) {
                if ($y == $x->getId()) {
                    $x->setDownloads($x->getDownloads() + 1);
                    $selected_files[] = $x;
                }
            }
        }
        $manager->flush();

        $to_zipFiles = array();
        foreach ($selected_files as $x) {
            $to_zipFiles[] = $x->getFileLocation();
        }

        $path = "./web/uploads/$identifier";
        mkdir($path);
        $download_path = "$path/$identifier.zip";
        $result = self::create_zip($to_zipFiles, $download_path, true);

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "result" => $result,
            "files" => $to_zipFiles,
            "server_path" => $path,
            "download_path" => $download_path
        ));
    }

    public static function removeIdentifier($c, $identifier) {
        $doctrine = $c->getDoctrine();
        $man = $doctrine->getManager();

        $repo = $doctrine->getRepository("AcmeSFSBundle:SFSUploads");
        $result = $repo->findBy(array(
            "identifier" => $identifier
        ));
        foreach ($result as $value) {
            $man->remove($value);
        }
        $man->flush();
    }

    /**
     * @Route("/service/uploader/count", name="url_uploadercounter",  defaults={"_format": "json"})
     * @Template()
     */
    public function countAction() {
        $id = $this->getRequest()->get("id", 0) + 0;
        $manager = $this->getDoctrine()->getManager();
        $uploader = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");

        $el = $uploader->find($id);

        if ($el !== null) {
            $el->setDownloads($el->getDownloads() + 1);
            $manager->persist($el);
            $manager->flush();
            return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                "count" => $el->getDownloads()
            ));
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "count" => "none"
        ));
    }

    /**
     * @Route("/service/uploader/remove", name="url_uploaderremove",  defaults={"_format": "json"})
     * @Template()
     */
    public function removeAction() {
        $id = $this->getRequest()->get("id", null);
        $identifier = $this->getRequest()->get("identifier", null);

        $manager = $this->getDoctrine()->getManager();
        $uploader = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");
        UtilsController::includeSFSUtil("EntityUtils");

        $elements = array();
        if ($identifier !== null) {
            $elements = $uploader->findBy(array(
                "identifier" => $identifier
            ));
        }
        if ($id !== null) {
            $elements[] = $uploader->findOneBy(array(
                "id" => $id
            ));
        }
        $counted = count($elements);
        $deleted = 0;
        $last_path = "";
        foreach ($elements as $value) {
            $path = $value->getFileLocation();
            //$path = realpath($path);
            $last_path = $path; // = "./" . $path;

            if (is_readable($path) && unlink($path)) {
                ++$deleted;
            }
            $manager->remove($value);
        }
        $manager->flush();

        return new \Symfony\Component\HttpFoundation\Response(json_encode(array(
                    "result" => $deleted,
                    "counted" => $counted,
                    "realpath" => $last_path
        )));
    }

    /**
     * @Route("/uploader", name="url_uploader")
     * @Template()
     */
    public function uploaderAction($input_id, $form_id, $identifier, $service = false, $remove_secure = false, $compact = false, $disable_delete = false) {
        return $this->render("AcmeSFSBundle:Uploader:uploader.html.twig", array(
                    "identifier" => $identifier,
                    "input_id" => $input_id,
                    "form_id" => $form_id,
                    "service" => $service,
                    "remove_secure" => $remove_secure,
                    "disable_delete" => $disable_delete,
                    "compact" => $compact
        ));
    }

    /**
     * @Route("/service/uploader/get", name="url_uploaderget")
     */
    public function getAction() {
        $id = $this->getRequest()->get("id", null);
        $identifier = $this->getRequest()->get("identifier", null);
        $uploader = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");
        UtilsController::includeSFSUtil("EntityUtils");
        $data = array();
        $array = array();
        if ($identifier !== null) {
            $data = $uploader->findBy(array(
                "identifier" => $identifier
                    ), array("id" => "asc"));
            foreach ($data as $value) {
                $array[] = self::getFormattedUploader($value);
            }
        }
        if ($id !== null) {
            $data = $uploader->findBy(array(
                "id" => $id
                    ), array("id" => "desc"));
            $array = self::getFormattedUploader($data);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "data" => $array
        ));
    }

    /**
     * @Route("/service/upload", name="url_uploadfiles",  defaults={"_format": "json"})
     * @Template()
     */
    public function uploadAction() {
        UtilsController::includeSFSUtil("Utils");
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");
        $manager = $this->getDoctrine()->getManager();

        $identifier = $this->getRequest()->get("identifier", "");
        $filename = $this->getRequest()->get("name", "");
        $list = array();

        foreach ($this->getRequest()->files as $uploadedFile) {
            $fileDB = new \Acme\SFSBundle\Entity\SFSUploads();

            //$file_server = $file_path . $name;

            $fileDB->setIdentifier($identifier);
            $fileDB->setFile($uploadedFile);
            /*
              $fileDB->setExtension($file_extension);
              $fileDB->setName($file_name);
              $fileDB->setOriginal($file_original);
              $fileDB->setPath($file_path);
              $fileDB->setSize($file_size);
             */

            $manager->persist($fileDB);
            $manager->flush();
            //$fileDB->setServer($file_server);


            $list[] = self::getFormattedUploader($fileDB);
        }
        return new \Symfony\Component\HttpFoundation\Response(json_encode(array(
                    "uploaded_files" => $list
        )));
    }

    public static function getFormattedUploader($fileDB) {
        if ($fileDB === null)
            return array();

        UtilsController::includeSFSUtil("Utils");
        UtilsController::includeSFSUtil("EntityUtils");
        $date = $fileDB->getDate();

        return array(
            "identifier" => $fileDB->getIdentifier(),
            "name" => $fileDB->getName(),
            "download_path" => $fileDB->getDownloadPath(),
            "size" => $fileDB->getSize(),
            "size_formatted" => \Utils::formatSizeUnits($fileDB->getSize()),
            "extension" => strtolower($fileDB->getExtension()),
            "original_name" => $fileDB->getOriginal(),
            "date" => $date->format("Y-m-d H:i:s"),
            "id" => $fileDB->getId(),
            "downloads" => $fileDB->getDownloads()
                //"server_path" => $file_server
        );
    }

}
