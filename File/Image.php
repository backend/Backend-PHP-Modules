<?php
namespace Backend\Modules\File;
class Image
{
    protected $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');

    protected $path;
    protected $name;
    protected $type;
    protected $width;
    protected $height;
    protected $size;

    public function __construct($path)
    {
        $this->path = $path;
        $this->name = basename($path);

        $this->validate();
    }

    protected function validate()
    {
        list($width, $height, $type) = getimagesize($this->path);
        if (empty($width) || empty($height)) {
            throw new \RuntimeException("Could not determine image dimensions");
        }
        $this->width = $width;
        $this->height = $height;
        $this->type = image_type_to_mime_type($type);

        // TODO Check image resolution
        /*
        list($this->width, $this->height) = getimagesize($uploaded_file);
        if (is_int($this->width)) {
            if ($this->options['max_width'] && $this->width > $this->options['max_width'] ||
                    $this->options['max_height'] && $this->height > $this->options['max_height']) {
                throw new \RuntimeError('Image Error: Too High Resolution', 400);
            }
            if ($this->options['min_width'] && $this->width < $this->options['min_width'] ||
                    $this->options['min_height'] && $this->height < $this->options['min_height']) {
                throw new \RuntimeError('Image Error: Too Low Resolution', 400);
            }
        }
        */
        return true;
    }

    public function scale($location, $width, $height)
    {
        if (substr($location, -1) !== DIRECTORY_SEPARATOR) {
            $location .= DIRECTORY_SEPARATOR;
        }
        $location .= $this->name;


        $scale = min(
            $width / $this->width,
            $height / $this->height
        );
        // Check the scale
        if ($scale >= 1) {
            if ($this->path !== $location) {
                return copy($this->path, $location);
            }
            return true;
        }
        $newWidth  = $this->width  * $scale;
        $newHeight = $this->height * $scale;

        $newImg = imagecreatetruecolor($newWidth, $newHeight);
        switch ($this->type) {
            case 'image/jpg':
            case 'image/jpeg':
                $srcImg = imagecreatefromjpeg($this->path);
                $writeImage = 'imagejpeg';
                $imageQuality = 75; // TODO Make this configurable
                break;
            case 'image/gif':
                imagecolortransparent($newImg, imagecolorallocate($newImg, 0, 0, 0));
                $srcImg = imagecreatefromgif($this->path);
                $writeImage = 'imagegif';
                $imageQuality = null;
                break;
            case 'image/png':
                imagecolortransparent($newImg, imagecolorallocate($newImg, 0, 0, 0));
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                $srcImg = imagecreatefrompng($this->path);
                $writeImage = 'imagepng';
                $imageQuality = 9; // TODO Make this configurable
                break;
            default:
                $srcImg = null;
        }
        $success = $srcImg
            && imagecopyresampled(
                $newImg,
                $srcImg,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $this->width,
                $this->height
            )
            && $writeImage($newImg, $location, $imageQuality);
        // Free up memory (imagedestroy does not delete files):
        imagedestroy($srcImg);
        imagedestroy($newImg);
        return $success;

    }
}