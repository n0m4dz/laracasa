<?php

/**
 * Created by PhpStorm.
 * User: n0m4dz
 * Date: 9/20/14
 * Time: 1:17 PM
 */

namespace N0m4dz\Laracasa;
set_include_path(__DIR__ . '/../../../' . PATH_SEPARATOR . get_include_path());

/**
 * Class including
 */

//Core classes
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Gdata/Photos.php';
require_once 'Zend/Gdata/App/Exception.php';

//User class
require_once 'Zend/Gdata/Photos/UserEntry.php';
require_once 'Zend/Gdata/Photos/UserFeed.php';
require_once 'Zend/Gdata/Photos/UserQuery.php';

//Album class
require_once 'Zend/Gdata/Photos/AlbumEntry.php';
require_once 'Zend/Gdata/Photos/AlbumFeed.php';
require_once 'Zend/Gdata/Photos/AlbumQuery.php';

//Photo class
require_once 'Zend/Gdata/Photos/PhotoEntry.php';
require_once 'Zend/Gdata/Photos/PhotoFeed.php';
require_once 'Zend/Gdata/Photos/PhotoQuery.php';

/**
 * Class declaration
 */

//Core class
use Illuminate\Support\Facades\Config;
use Zend_Gdata_ClientLogin;
use Zend_Gdata_Photos;
use Zend_Gdata_App_Exception;

//User class
use Zend_Gdata_Photos_UserEntry;
use Zend_Gdata_Photos_UserFeed;
use Zend_Gdata_Photos_UserQuery;

//Album class
use Zend_Gdata_Photos_AlbumEntry;
use Zend_Gdata_Photos_AlbumFeed;
use Zend_Gdata_Photos_AlbumQuery;

//Photo class
use Zend_Gdata_Photos_PhotoEntry;
use Zend_Gdata_Photos_PhotoFeed;
use Zend_Gdata_Photos_PhotoQuery;

class Laracasa
{
    private $service;
    private $client;
    private $user;
    private $pass;
    private $album;
    
    /**
     * Constructor function
     */
    public function __construct() {
        
        // Parameters for ClientAuth authentication
        $service_name = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
        $this->user = html_entity_decode(Config::get('laracasa::user'));
        $this->pass = html_entity_decode(Config::get('laracasa::password'));
        $this->album = html_entity_decode(Config::get('laracasa::album'));
        try {
            $this->service = new Zend_Gdata_Photos($this->client);
            $this->client = Zend_Gdata_ClientLogin::getHttpClient($this->user, $this->pass, $service_name);
        }
        catch(Zend_Gdata_App_Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    /**
     * Retrieve photos from specified album
     */    
    function getAlbum() {
        $photos = new Zend_Gdata_Photos($this->client);
        $query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setUser($this->user);
        $query->setAlbumId($this->album);
        $albumFeed = $photos->getAlbumFeed($query);
        return $albumFeed;
    }
    
    /**
     * Select a photo from specified album
     * @param  [string] $photoId [photo ID]
     * @return [object]          [return photo object]
     */
    function getPhotoById($photoId) {
        $photos = new Zend_Gdata_Photos($this->client);
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser($this->user);
        $query->setAlbumId($this->album);
        $query->setPhotoId($photoId);
        $query = $query->getQueryUrl() . "?kind=comment,tag&imgmax=1600";
        
        $photoFeed = $photos->getPhotoFeed($query);
        return $photoFeed;
    }
    
    /**
     * Add a photo to specific picasa web album
     * @param [file] $photo [uploaded photo file object]
     */
    function addPhoto($photo) {
        if (!file_exists($photo['tmp_name']) || !is_uploaded_file($photo['tmp_name'])) {
            $o = array('state' => false); 
        } else {
            
            $photos = new Zend_Gdata_Photos($this->client);
            
            $fd = $photos->newMediaFileSource($photo["tmp_name"]);
            $fd->setContentType($photo["type"]);
            
            $entry = new Zend_Gdata_Photos_PhotoEntry();
            $entry->setMediaSource($fd);
            $entry->setTitle($photos->newTitle($photo["name"]));
            
            $albumQuery = new Zend_Gdata_Photos_AlbumQuery;
            $albumQuery->setUser($this->user);
            $albumQuery->setAlbumId($this->album);
            
            $albumEntry = $photos->getAlbumEntry($albumQuery);
            
            $result = $photos->insertPhotoEntry($entry, $albumEntry);
            if ($result) {
                $o = array('state' => true, 'id' => $result->getGphotoId());                 
            } else {
                $o = array('state' => false); 
            }            
        }
        return $o;
    }
    
    /**
     * Deletes the specified photo
     *
     * @param  Zend_Http_Client $client  The authenticated client
     * @param  string           $user    The user's account name
     * @param  integer          $albumId The album's id
     * @param  integer          $photoId The photo's id
     * @return void
     */
    function deletePhoto($photoId) {
        $photos = new Zend_Gdata_Photos($this->client);
        
        $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
        $photoQuery->setUser($this->user);
        $photoQuery->setAlbumId($this->album);
        $photoQuery->setPhotoId($photoId);
        $photoQuery->setType('entry');
       
        $entry = $photos->getPhotoEntry($photoQuery);
        
        $photos->deletePhotoEntry($entry, true);
    }
}
?>

