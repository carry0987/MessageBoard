<?php
class Thumbnail
{
    private $imageLibrary = 'GD';
    private $field_name = 'image';
    private $file_name = '';
    private $remove_origin = false;
    private $thumb = false;
    private $file_ext = '';
    private $thumb_height = 0;
    private $thumb_width = 200;
    private $thumb_path = '';
    private $origin_path = '';
    private $setRandom = false;

    public function __construct($thumb = true)
    {
        $this->thumb = $thumb;
    }

    public function setImageLibrary($value = 'GD')
    {
        switch ($value) {
            case 'GD':
                if (extension_loaded('gd') || extension_loaded('gd2')) {
                    $this->imageLibrary = 'GD';
                } else {
                    $this->imageLibrary = false;
                }
                break;
            case 'Imagick':
                if (extension_loaded('imagick')) {
                    $this->imageLibrary = 'Imagick';
                } else {
                    $this->imageLibrary = false;
                }
                break;
            default:
                if (extension_loaded('gd') || extension_loaded('gd2')) {
                    $this->imageLibrary = 'GD';
                } else {
                    $this->imageLibrary = false;
                }
                break;
        }
        return $this->imageLibrary;
    }

    public function setThumb($path, $height = '', $width = '')
    {
        $this->thumb_path = $path;
        $this->thumb_height = (!empty($height) && $height !== 0) ? $height : 0;
        $this->thumb_width = (!empty($width)) ? $width : 200;
    }

    public function setRandom($set_random = false)
    {
        $this->setRandom = $set_random;
    }

    public function getImageUpload($getFile, $target_folder = '', $remove_origin = false)
    {
        $this->remove_origin = $remove_origin;
        //Folder path setup
        $target_path = $target_folder;
        $this->origin_path = $target_folder;
        $thumb_path = $this->thumb_path;
        if (is_array($getFile[$this->field_name]['name'])) {
            foreach ($getFile[$this->field_name]['name'] as $key => $value) {
                //File name setup
                $filename_err = explode('.', $getFile[$this->field_name]['name'][$key]);
                $filename_err_count = count($filename_err);
                $file_ext = $filename_err[$filename_err_count - 1];
                if ($this->file_name != '') {
                    $fileName = $this->file_name.'.'.$file_ext;
                } else {
                    $fileName = $getFile[$this->field_name]['name'][$key];
                }
                //Upload image path
                $fileName = str_replace(' ', '_' ,$fileName);
                if ($this->setRandom === true) {
                    $randomName = $this->generateRandom(8).'.'.$file_ext;
                    $upload_image = $target_path.basename($randomName);
                    $files['file_name'] = $randomName;
                } else {
                    $upload_image = $target_path.basename($fileName);
                    $files['file_name'] = $fileName;
                }
                //Upload image
                $this->file_ext = $file_ext;
                $files['tmp_name'] = $getFile[$this->field_name]['tmp_name'][$key];
                $thumbFile['origin_name'][] = $fileName;
                $thumbFile['thumbnail'][] = $this->createThumbnail($files, $upload_image, $this->imageLibrary);
                $thumbFile['file_name'][] = $files['file_name'];
            }
        } else {
                //File name setup
                $filename_err = explode('.', $getFile[$this->field_name]['name']);
                $filename_err_count = count($filename_err);
                $file_ext = $filename_err[$filename_err_count - 1];
                if ($this->file_name != '') {
                    $fileName = $this->file_name.'.'.$file_ext;
                } else {
                    $fileName = $getFile[$this->field_name]['name'];
                }
                //Upload image path
                $fileName = str_replace(' ', '_' ,$fileName);
                if ($this->setRandom === true) {
                    $randomName = $this->generateRandom(8).'.'.$file_ext;
                    $upload_image = $target_path.basename($randomName);
                    $files['file_name'] = $randomName;
                } else {
                    $upload_image = $target_path.basename($fileName);
                    $files['file_name'] = $fileName;
                }
                //Upload image
                $this->file_ext = $file_ext;
                $files['tmp_name'] = $getFile[$this->field_name]['tmp_name'];
                $thumbFile['origin_name'] = $fileName;
                $thumbFile['thumbnail'] = $this->createThumbnail($files, $upload_image, $this->imageLibrary);
                $thumbFile['file_name'] = $files['file_name'];
        }
        return $thumbFile;
    }

    public function getImageLocal($getFile, $target_folder = '')
    {
        $this->remove_origin = false;
        //Folder path setup
        $target_path = $target_folder;
        $this->origin_path = $target_folder;
        $thumb_path = $this->thumb_path;
        //File name setup
        $filename_err = explode('.', $getFile['name']);
        $filename_err_count = count($filename_err);
        $file_ext = $filename_err[$filename_err_count - 1];
        if ($this->file_name != '') {
            $fileName = $this->file_name.'.'.$file_ext;
        } else {
            $fileName = $getFile['name'];
        }
        //Upload image path
        $fileName = str_replace(' ', '_' ,$fileName);
        $upload_image = $target_path.basename($fileName);
        $files['file_name'] = $fileName;
        //Upload image
        $this->file_ext = $file_ext;
        $thumbFile['origin_name'] = $fileName;
        $thumbFile['thumbnail'] = $this->createThumbnailLocal($files, $upload_image, $this->imageLibrary);
        $thumbFile['file_name'] = $files['file_name'];
        return $thumbFile;
    }

    private function createThumbnailLocal($fileArray, $upload_image, $image_library)
    {
        //Thumbnail creation
        if ($this->thumb === true) {
            $fileName = str_replace('.'.$this->file_ext, '_thumb.'.$this->file_ext, $fileArray['file_name']);
            $thumbnail = $this->thumb_path.$fileName;
            $fileName = $thumbnail;
            if ($this->isImage($upload_image) === false) {
                $this->imageLibrary = false;
                $fileName = false;
            }
            if ($this->imageLibrary === 'GD') {
                //Create with GD
                $checkResult = $this->createWithGD($thumbnail, $upload_image);
                if ($checkResult === false) {
                    $fileName = false;
                } elseif ($checkResult === null) {
                    $fileName = $this->origin_path.$fileArray['file_name'];
                }
            } elseif ($this->imageLibrary === 'Imagick') {
                //Create with Imagick
                $checkResult = $this->createWithImagick($thumbnail, $upload_image);
                if ($checkResult === false) {
                    $fileName = false;
                } elseif ($checkResult === null) {
                    $fileName = $this->origin_path.$fileArray['file_name'];
                }
            } else {
                $fileName = false;
            }
        } else {
            $fileName = $this->origin_path.$fileArray['file_name'];
        }
        return $fileName;
    }

    private function createThumbnail($fileArray, $upload_image, $image_library)
    {
        if (move_uploaded_file($fileArray['tmp_name'], $upload_image)) {
            //Thumbnail creation
            if ($this->thumb === true) {
                $fileName = str_replace('.'.$this->file_ext, '_thumb.'.$this->file_ext, $fileArray['file_name']);
                $thumbnail = $this->thumb_path.$fileName;
                $fileName = $thumbnail;
                if ($this->isImage($upload_image) === false) {
                    $this->imageLibrary = false;
                    $fileName = false;
                }
                if ($this->imageLibrary === 'GD') {
                    //Create with GD
                    $checkResult = $this->createWithGD($thumbnail, $upload_image);
                    if ($checkResult === false) {
                        $fileName = false;
                    } elseif ($checkResult === null) {
                        $fileName = $this->origin_path.$fileArray['file_name'];
                    }
                } elseif ($this->imageLibrary === 'Imagick') {
                    //Create with Imagick
                    $checkResult = $this->createWithImagick($thumbnail, $upload_image);
                    if ($checkResult === false) {
                        $fileName = false;
                    } elseif ($checkResult === null) {
                        $fileName = $this->origin_path.$fileArray['file_name'];
                    }
                } else {
                    $fileName = false;
                }
            } else {
                $fileName = $this->origin_path.$fileArray['file_name'];
            }
            return $fileName;
        } else {
            return false;
        }
    }

    private function createWithGD($thumbnail, $upload_image)
    {
        list($width, $height) = getimagesize($upload_image);
        if ($this->checkSize($width, $height) === true) {
            //Keep aspect ratio
            $thumb_height = ($this->thumb_height === 0) ? ceil(($this->thumb_width / $width) * $height) : $this->thumb_height;
            $thumb_create = imagecreatetruecolor($this->thumb_width, $thumb_height);
            $source = false;
            switch ($this->file_ext) {
                case 'jpg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'jpeg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'png':
                    $background = imagecolorallocate($thumb_create , 0, 0, 0);
                    imagecolortransparent($thumb_create, $background);
                    imagealphablending($thumb_create, false);
                    imagesavealpha($thumb_create, true);
                    $source = imagecreatefrompng($upload_image);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($upload_image);
                    break;
                default:
                    $source = imagecreatefromjpeg($upload_image);
                    break;
            }
            if ($source !== false) {
                imagecopyresampled($thumb_create, $source, 0, 0, 0, 0, $this->thumb_width, $thumb_height, $width, $height);
                switch ($this->file_ext) {
                    case 'jpg':
                        $result = imagejpeg($thumb_create, $thumbnail, 80);
                        break;
                    case 'jpeg':
                        $result = imagejpeg($thumb_create, $thumbnail, 80);
                        break;
                    case 'png':
                        $result = imagepng($thumb_create, $thumbnail, 9);
                        break;
                    case 'gif':
                        $result = imagegif($thumb_create, $thumbnail);
                        break;
                    default:
                        $result = imagejpeg($thumb_create, $thumbnail, 80);
                        break;
                }
                imagedestroy($source);
                imagedestroy($thumb_create);
                if ($this->remove_origin === true && file_exists($upload_image)) {
                    unlink($upload_image);
                }
            } else {
                $result = false;
            }
        } else {
            $result = null;
        }
        return $result;
    }

    private function createWithImagick($thumbnail, $upload_image)
    {
        $Imagick = new Imagick($upload_image);
        $format = $Imagick->getImageFormat();
        $getSize = $Imagick->getImageGeometry();
        $width = $getSize['width'];
        $height = $getSize['height'];
        if ($this->checkSize($width, $height) === true) {
            //Keep aspect ratio
            $thumb_height = ($this->thumb_height === 0) ? ceil(($this->thumb_width / $width) * $height) : $this->thumb_height;
            $source = false;
            if ($format !== false && $getSize !== false) {
                //Strip out unneeded meta data
                $Imagick->stripImage();
                switch ($format) {
                    case 'JPG':
                        $Imagick->resizeImage($this->thumb_width, $thumb_height, Imagick::FILTER_LANCZOS, 1);
                        $Imagick->setImageFormat('jpg');
                        //Set compression level (1 lowest quality, 100 highest quality)
                        $Imagick->setImageCompressionQuality(80);
                        $result = $Imagick->writeImage($thumbnail);
                        break;
                    case 'JPEG':
                        $Imagick->resizeImage($this->thumb_width, $thumb_height, Imagick::FILTER_LANCZOS, 1);
                        $Imagick->setImageFormat('jpg');
                        $Imagick->setImageCompressionQuality(80);
                        $result = $Imagick->writeImage($thumbnail);
                        break;
                    case 'PNG':
                        $Imagick->resizeImage($this->thumb_width, $thumb_height, Imagick::FILTER_LANCZOS, 1);
                        $Imagick->setImageFormat('png');
                        $Imagick->setCompressionQuality(0);
                        $result = $Imagick->writeImage($thumbnail);
                        break;
                    case 'GIF':
                        $Imagick->setImageFormat('gif');
                        $Imagick = $Imagick->coalesceImages();
                        $resize_x = ceil($this->thumb_width / 2);
                        $resize_y = ceil($thumb_height / 2);
                        foreach ($Imagick as $frame) {
                            $frame->scaleImage($this->thumb_width, $thumb_height, $resize_x, $resize_y);
                            $frame->setImagePage(0, 0, 0, 0);
                        }
                        $Imagick = $Imagick->deconstructImages();
                        $result = $Imagick->writeImages($thumbnail, true);
                        break;
                    default:
                        $Imagick->resizeImage($this->thumb_width, $thumb_height, Imagick::FILTER_LANCZOS, 1);
                        $Imagick->setImageFormat('jpg');
                        $Imagick->setImageCompressionQuality(80);
                        $result = $Imagick->writeImage($thumbnail);
                        break;
                }
                $Imagick->clear();
                if ($this->remove_origin === true && file_exists($upload_image)) {
                    unlink($upload_image);
                }
            } else {
                $result = false;
            }
        } else {
            $result = null;
        }
        return $result;
    }

    private function isImage($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
        if (in_array($image_type , array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
            return true;
        }
        return false;
    }

    private function checkSize($width, $height)
    {
        if ($width <= $this->thumb_width && $height <= $this->thumb_height) {
            return false;
        }
        return true;
    }

    private function generateRandom($length, $numeric = 0)
    {
        $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash = $hash.$seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}
