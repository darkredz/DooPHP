<?php
/**
 * DooGdImage class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * A helper class that helps to manage images, upload, resize, create thumbnails, crop images, etc.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooGdImage.php 1000 2009-08-19 21:37:38
 * @package doo.helper
 * @since 1.1
 */
class DooGdImage {
    
    /**
     * Path to store the uploaded image files
     * @var string
     */
    public $uploadPath;

    /**
     * Path to store the resized/processed image files(thumbnails/watermarks)
     * @var string
     */
    public $processPath;

    /**
     * Suffix name for the generated thumbnail image files, eg. 203992029_thumb.jpg
     * @var string
     */
    public $thumbSuffix = '_thumb';

    /**
     * Suffix name for the generated watermarked image files, eg. 203992029_water.jpg
     * @var string
     */
    public $waterSuffix = '_water';

    /**
     * Suffix name for the cropped image files, eg. 203992029_crop.jpg
     * @var string
     */
    public $cropSuffix = '_crop';

    /**
     * Determine whether to save the processed images
     * @var bool
     */
    public $saveFile = true;

    /**
     * The file name of the TTF font to be used with watermarks.
     * @var string
     */
    public $ttfFont;

    /**
     * Generated image type. gif, png, jpg
     * @var string
     */
    public $generatedType = 'jpg';

    /**
     * Generated image quality. For png & jpg only
     * @var string
     */
    public $generatedQuality = 100;

    /**
     * Determine whether to use time as file name for the uploaded images.
     * @var bool
     */
    public $timeAsName = true;

    /**
     * Construtor for DooGdImage
     *
     * @param string $uploadPath Path of the uploaded image
     * @param string $processPath Path to save the processes images
     * @param bool $saveFile To save the processed images
     */
    function  __construct($uploadPath='', $processPath='', $saveFile=true, $timeAsName=true){
        $this->uploadPath = $uploadPath;
        $this->processPath = $processPath;
        $this->saveFile = $saveFile;
        $this->timeAsName = $timeAsName;
    }

    /**
     * Set the TTF Font type for water mark processing.
     * @param string $ttfFontFile the TTF font file name
     * @param string $path Path to the font
     */
    public function setFont($ttfFontFile, $path=''){
        $this->ttfFont = $path . $ttfFontFile;
    }

    /**
     * Creates an image object based on the file type.
     *
     * @param mixed The variable to store the image resource created.
     * @param string $type Image type, gif, png, jpg
     * @param string $file File name of the image
     * @return resource
     */
    protected function createImageObject(&$img, $type, $file){
        switch($type){
            case 1:
                $img = imagecreatefromgif($file);
                break;
            case 2:
                $img = imagecreatefromjpeg($file);
                break;
            case 3:
                $img = imagecreatefrompng($file);
                break;
            default:
                return false;
        }
        return $img;
    }

    /**
     * Creates an image from a resouce based on the generatedType
     * 
     * @param resource $img The image resource
     * @param string $file File name to be stored.
     * @return bool
     */
    protected function generateImage(&$img, $file=null){
        switch($this->generatedType){
            case 'gif':
                return imagegif($img, $file);
                break;
            case 'jpg':
                return imagejpeg($img, $file, $this->generatedQuality);
                break;
            case 'png':
                return imagepng($img, $file, $this->generatedQuality);
                break;
            default:
                return false;
        }
    }

    /**
     * Crops an image.
     * 
     * @param string $file Image file name
     * @param int $cropWidth Width to be cropped
     * @param int $cropHeight Height to be cropped
     * @param int $cropStartX Position x to start cropping
     * @param int $cropStartY Position y to start cropping
     * @return bool|string Returns the generated image file name. Return false if failed.
     */
    public function crop($file, $cropWidth, $cropHeight, $cropStartX=0, $cropStartY=0){
        $file = $this->uploadPath . $file;
        $imginfo = $this->getInfo($file);
        $newName = substr($imginfo['name'], 0, strrpos($imginfo['name'], '.')) . $this->cropSuffix .'.'. $this->generatedType;

        //create image object based on the image file type, gif, jpeg or png
        $this->createImageObject($img, $imginfo['type'], $file);

        if(!$img) return false;

        $cropimg = imagecreatetruecolor($cropWidth,$cropHeight);
        $width = $imginfo['width'];
        $height = $imginfo['height'];

        //Crop now
        imagecopyresized($cropimg, $img, 0, 0, $cropStartX, $cropStartY, $width, $height, $width, $height);

        if($this->saveFile){
            //delete if exist
            if(file_exists($this->processPath . $newName))
                unlink($this->processPath . $newName);
            $this->generateImage($cropimg, $this->processPath . $newName);
            imagedestroy($cropimg);
            imagedestroy($img);
            return $this->processPath . $newName;
        }
        else{
            $this->generateImage($cropimg);
            imagedestroy($cropimg);
            imagedestroy($img);
        }

        return true;
    }

    /**
     * Resize/Generates thumbnail from an existing image file.
     * 
     * @param string $file The image file name.
     * @param int $width Width of the thumbnail
     * @param int $height Height of the thumbnail
     * @return bool|string Returns the generated image file name. Return false if failed.
     */
    public function createThumb($file, $width=128, $height=128){
        $file = $this->uploadPath . $file;
        $imginfo = $this->getInfo($file);
        $newName = substr($imginfo['name'], 0, strrpos($imginfo['name'], '.')) . $this->thumbSuffix .'.'. $this->generatedType;

        //create image object based on the image file type, gif, jpeg or png
        $this->createImageObject($img, $imginfo['type'], $file);

        if(!$img) return false;

        $width  = ($width > $imginfo['width']) ? $imginfo['width'] : $width;
        $height = ($height > $imginfo['height']) ? $imginfo['height'] : $height;
        $oriW = $imginfo['width'];
        $oriH = $imginfo['height'];

        //maintain ratio
        if($oriW*$width > $oriH*$height)
            $height = round($oriH * $width/$oriW);
        else
            $width = round($oriW * $height/$oriH);

        //For GD version 2.0.1 only
        if (function_exists('imagecreatetruecolor')){
            $newImg = imagecreatetruecolor($width, $height);
            imagecopyresized($newImg, $img, 0, 0, 0, 0, $width, $height, $imginfo['width'], $imginfo['height']);
        }
        else{
            $newImg = imagecreate($width, $height);
            imagecopyresized($newImg, $img, 0, 0, 0, 0, $width, $height, $imginfo['width'], $imginfo['height']);
        }

        if($this->saveFile){
            //delete if exist
            if(file_exists($this->processPath . $newName))
                unlink($this->processPath . $newName);
            $this->generateImage($newImg, $this->processPath . $newName);
            imagedestroy($newImg);
            imagedestroy($img);
            return $this->processPath . $newName;
        }
        else{
            $this->generateImage($newImg);
            imagedestroy($newImg);
            imagedestroy($img);
        }
        
        return true;
    }

    /**
     * Add water mark text to an image.
     * 
     * @param string $file Image file name
     * @param string $text Text to be added as water mark
     * @param int $maxWidth Maximum width of the processed image
     * @param int $maxHeight Maximum height of the processed image
     * @return bool|string Returns the generated image file name. Return false if failed.
     */
    public function waterMark($file, $text, $maxWidth, $maxHeight){
        $file = $this->uploadPath . $file;
        $imginfo = $this->getInfo($file);
        $newName = substr($imginfo['name'], 0, strrpos($imginfo['name'], '.')) . $this->thumbSuffix .'.'. $this->generatedType;

        //create image object based on the image file type, gif, jpeg or png
        $this->createImageObject($img, $imginfo['type'], $file);

        if(!$img) return false;

        $width  = ($maxWidth > $imageInfo['width']) ? $imageInfo['width'] : $maxWidth;
        $height = ($maxHeight > $imageInfo['height']) ? $imageInfo['height'] : $maxHeight;
        $oriW	= $imageInfo['width'];
        $oriH	= $imageInfo['height'];

        if ($oriW*$width > $oriH*$height)
            $height = round($oriH*$width/$oriW);
        else
            $width = round($oriW*$height/$oriH);

        if (function_exists('imagecreatetruecolor')){
            $new = imagecreatetruecolor($width, $height);
            imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo['width'], $imageInfo['height']);
        }
        else{
            $new = imagecreate($width, $height);
            imagecopyresized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo['width'], $imageInfo['height']);
        }

        $white = imagecolorallocate($new, 255, 255, 255);
        $black = imagecolorallocate($new, 0, 0, 0);
        $alpha = imagecolorallocatealpha($new, 230, 230, 230, 40);

        imagefilledrectangle($new, 0, $height-26, $width, $height, $alpha);
        imagefilledrectangle($new, 13, $height-20, 15, $height-7, $black);
        imageTTFText($new, 4.9, 0, 20, $height-14, $black, $this->ttfFont, $text[0]);
        imageTTFText($new, 4.9, 0, 20, $height-6, $black, $this->ttfFont, $text[1]);

        if ($this->saveFile){
            if (file_exists($this->processPath . $newName))
                unlink($this->processPath . $newName);
            $this->generateImage($newImg, $this->processPath . $newName);
            imagedestroy($new);
            imagedestroy($img);
            return $this->processPath . $newName;
        }
        else{
            $this->generateImage($newImg);
            imagedestroy($new);
            imagedestroy($img);
        }

        return true;
    }

    /**
     * Get the file name of an image's thumbnail.
     * @param string $file Image file name
     * @return string
     */
    public function getThumb($file){
        $thumbName = substr($file, 0, strrpos($file, '.')) . $this->thumbSuffix .'.'. $this->generatedType;
        $file = $this->processPath . $thumbName;
        if(!file_exists($file))
            return;
        return $file;
    }

    /**
     * Get the file name of an image's watermark.
     * @param string $file Image file name
     * @return string
     */
    public function getWaterMark($file){
        $markName = substr($file, 0, strrpos($file, ".")) . $this->waterSuffix .'.'. $this->generatedType;
        $file = $this->processPath . $markName;
        if (!file_exists($file))
            return;
        return $file;
    }

    /**
     * Tries to remove the images along with its thumbnails & watermarks
     *
     * @param string|array $file Image file name(s)
     * @return string
     */
    public function removeImage($file){
        if(is_array($file)){
            foreach($file as $f){
                $oriName   = $this->processPath . $f;
                $thumbName	= $this->processPath . substr($f, 0, strrpos($f, '.')) . $this->thumbSuffix .'.'. $this->generatedType;
                $markName  = $this->processPath . substr($f, 0, strrpos($f, '.')) . $this->waterSuffix .'.'. $this->generatedType;

                if(file_exists($thumbName))
                    unlink($thumbName);

                if(file_exists($markName))
                    unlink($markName);

                if(file_exists($oriName))
                    unlink($oriName);
            }
        }
        else{
            $oriName   = $this->processPath . $file;
            $thumbName	= $this->processPath . substr($file, 0, strrpos($file, '.')) . $this->thumbSuffix .'.'. $this->generatedType;
            $markName  = $this->processPath . substr($file, 0, strrpos($file, '.')) . $this->waterSuffix .'.'. $this->generatedType;

            if(file_exists($thumbName))
                unlink($thumbName);

            if(file_exists($markName))
                unlink($markName);

            if(file_exists($oriName))
                unlink($oriName);
        }
    }


    /**
     * Get the info of an image
     *
     * @param string $file Image file name
     * @return array Returns info in array consists of width, height, type, name & filesize.
     */
    public function getInfo($file){
        $data = getimagesize($file);
        $img['width'] = $data[0];
        $img['height'] = $data[1];
        $img['type'] = $data[2];
        $img['name'] = basename($file);
        $img['filesize'] = filesize($file);
        return $img;
    }

    /**
     * Save the uploaded images in HTTP File Upload variables
     * 
     * @param string $filename The file field name in $_FILES HTTP File Upload variables
     * @return string The file name of the uploaded image.
     */
    public function uploadImage($filename){
        $img = !empty($_FILES[$filename]) ? $_FILES[$filename] : null;
        if($img==Null)return;

        if ($this->timeAsName){
            $pic = strrpos($img['name'], '.');
            $ext = substr($img['name'], $pic+1);
            $newName = date('Ymdhis') . '.' . $ext;
        }
        else{
            $newName = $img['name'];
        }

        $imgPath = $this->uploadPath . $newName;
        
        if (move_uploaded_file($img['tmp_name'], $imgPath))
            return $newName;
    }
}

?>