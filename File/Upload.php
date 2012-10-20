<?php
namespace Backend\Modules\File;
class Upload
{
    protected $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');

    protected $overwriteExisting = true;

    protected $tmpFile;
    protected $name;
    protected $type;
    protected $size;

    public function __construct($name, $type, $size, $tmpFile, $error)
    {
        $this->tmpFile = $tmpFile;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        if (empty($error) === false) {
            throw new \RuntimeException('File Upload Error: ' . $error);
        }

        $this->checkExtension();
        $this->validate();
    }

    protected function validate()
    {
        if (empty($this->name)) {
            throw new \RuntimeException('File Upload Error: Missing File Name');
        }

        // TODO Check file sizes
        /*
        if ($this->options['max_file_size'] && $file->size > $this->options['max_file_size']) {
            throw new \RuntimeException('File Upload Error: File Too Large', 400);
        }
        if ($this->options['min_file_size'] && $file_size < $this->options['min_file_size']) {
            throw new \RuntimeException('File Upload Error: File Too Small', 400);
        }
        */
        return true;
    }

    protected function checkExtension()
    {
        $regex = '/^.*\/(' . implode('|', $this->allowedTypes) . ')$/';
        if (preg_match($regex, $this->type, $matches) !== 1) {
            throw new \RuntimeException('File Upload Error: Invalid File Type', 400);
        }
        $ext = explode('.', $this->name);
        if (count($ext) === 1) {
            $this->name .= '.'.$matches[1];
        } else {
            $ext = end($ext);
            if ($ext !== $matches[1]) {
                $this->name = preg_replace('/' . $ext . '$/', $matches[1], $this->name);
            }
        }
        return $this->name;
    }

    public function uploadFile($location)
    {
        if (substr($location, -1) !== DIRECTORY_SEPARATOR) {
            $location .= DIRECTORY_SEPARATOR;
        }
        $location .= $this->name;
        if (move_uploaded_file($this->tmpFile, $location)) {
            return $location;
        }
        throw new \RuntimeException('Could not move Uploaded File', 500);
    }

    public function __get($propertyName)
    {
        return $this->$propertyName;
    }
}