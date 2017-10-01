<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSUploads
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class SFSUploads {
    public static function removeLike($manager, $like){
        //$manager = $doctrine->getManager();
        $qb = $manager->createQueryBuilder("c");
        $qb->select("c");
        $qb->from('AcmeSFSBundle:SFSUploads', 'c');
        $qb->where("c.identifier LIKE '$like'");
        $r = $qb->getQuery()->getResult();
        foreach ($r as $value) {
            $manager->remove($value);
        }
        $manager->flush();
    }
    
    public static function getDataDir(){
        $data_dir = ".";
        if(strlen(getEnv("OPENSHIFT_DATA_DIR")) > 1){
            $data_dir = substr(getEnv("OPENSHIFT_DATA_DIR"), 0, -1);
        }
        return $data_dir;
    }
    
    public function getFileLocation(){
        return self::getDataDir() . $this->getPath();
    }
    
    public function getDownloadPath(){
        if(strlen(getEnv("OPENSHIFT_DATA_DIR")) > 1){
            return "/uploader/download/" . $this->getId();
        }
        return $this->getPath();
    }

    private $file = null;
    private $path_server = "";

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile($file) {
        //$this->upload_dir = $path;
        $this->file = $file;
        
        // do whatever you want to generate a unique name
        $filename = sha1(uniqid(mt_rand(), true));
        $identifier = $this->getIdentifier();
        $name = $filename . "_" . $this->file->getClientOriginalName() . ".file";
        $this->path_server = "/web/uploads/$identifier/";
        
        $file_original = $this->file->getClientOriginalName();
        $file_name = $name;
        $file_path = "/web/uploads/$identifier/$name";
        $file_size = $this->file->getClientSize();
        $file_extension = $this->file->getClientOriginalExtension();

        $this->original = $file_original;
        $this->name = $file_name;
        $this->path = $file_path;
        $this->size = $file_size;
        $this->extension = $file_extension;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->file) {
            
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->file) {
            return;
        }
        
        $file = $this->file->move(dirname($this->getFileLocation()), $this->name);
        /* if (isset($this->temp)) {
          // delete the old image
          unlink($this->getUploadRootDir() . '/' . $this->file->path);
          // clear the temp image path
          $this->temp = null;
          } */
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        $file = $this->getFileLocation();
        if ($file && file_exists($file)) {
            try{
                unlink($file);
            } catch (\Exception $ex){
            }
        }else{
            //echo "\nFileNotExists: $file\nIn:" . __DIR__;
        }
    }

    public function __construct() {
        $this->date = new \DateTime();
        $this->downloads = 0;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=500)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=1000)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="original", type="string", length=1000)
     */
    private $original;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=100)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=2000)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     *
     * @ORM\Column(name="downloads", type="integer")
     */
    private $downloads;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return SFSUploads
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SFSUploads
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set original
     *
     * @param string $original
     * @return SFSUploads
     */
    public function setOriginal($original) {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return string 
     */
    public function getOriginal() {
        return $this->original;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return SFSUploads
     */
    public function setExtension($extension) {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return SFSUploads
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return SFSUploads
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return SFSUploads
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate() {
        return $this->date;
    }

    public function setDownloads($date) {
        $this->downloads = $date;
        return $this;
    }

    public function getDownloads() {
        return $this->downloads;
    }

}
