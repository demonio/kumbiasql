<?php
/**
 */
class _img extends _html
{
    private $image;
    private $imageType;

    // Carga la imagen según su tipo
    public function load($filename) {
        $imageInfo = getimagesize($filename);
        $this->imageType = $imageInfo[2];
        
        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_WEBP:
                $this->image = imagecreatefromwebp($filename);
                break;
            case IMAGETYPE_AVIF:
                $this->image = imagecreatefromavif($filename);
                break;
            default:
                return false;
        }
        return true;
    }

    // Redimensiona la imagen al ancho dado
    public function resizeToWidth($width) {
        $ratio = $width / imagesx($this->image);
        $height = imagesy($this->image) * $ratio;
        $this->resize($width, $height);
    }

    // Redimensiona la imagen a las dimensiones dadas
    public function resize($width, $height) {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));
        $this->image = $newImage;
    }

    // Guarda la imagen en formato WebP
    public function save($filename, $compression = 75, $permissions = null) {
        imagewebp($this->image, $filename, $compression);
        if ($permissions !== null) {
            chmod($filename, $permissions);
        }
    }

    // Procesa la imagen y la convierte a WebP en varios tamaños
    public function process($uploadedFile, $outputDir, $randomString, $sizes = [320, 576, 768, 992, 1200, 1400, 1920]) {
        if (!$this->load($uploadedFile)) {
            return null;
        }

        foreach ($sizes as $width) {
            $this->resizeToWidth($width);
            $webpFileName = "{$width}w.{$randomString}.webp";
            $this->save("$outputDir/$webpFileName");
            $this->load($uploadedFile); // Recargar la imagen original para la siguiente iteración
        }

        return "$randomString.webp";
    }

    # 0
    public static function icon($icon)
    {
        include "img/icons/$icon.svg";
    }

    # 1
	public static function src($dir, $photos, $imgs='', $width='', $height='')
	{        
        if ( ! $photos) {
            return $imgs;
        }

        if (strpos($photos, ',') !== false) {
            $photosArray  = explode(',', $photos);
            foreach ($photosArray  as $photo) {
                $photo = trim($photo);
                $imgs .= self::src($dir, $photo, $width, $height);
            }
        }
        else {
            $ext = strtolower(pathinfo($photos, PATHINFO_EXTENSION));
            if ($ext === 'svg') {
                $imgs .= parent::img($dir, $photos, $width, $height, $photos);
            }
            else {
                self::getImg($dir, $photos);
                $webpFileName = pathinfo($photos, PATHINFO_FILENAME) . '.webp';
                $imgs .= parent::img($dir, $webpFileName, $width, $height, $webpFileName);
            }
        }

        return $imgs; 
    }

    # 1.1
    public static function getImg($dir, $photos)
    {    
        if (self::existsImgInLocal($dir, $photos)) {
            return;
        }
        $urls = [
            "https://roleplus.app$dir/$photos",
            "http://roleplus.v3$dir/$photos",
        ];
        foreach ($urls as $url) {
            if ( ! self::existsImgInRemote($url)) {
                continue;
            }
            $data = file_get_contents($url);
            self::setWebp($dir, $photos, $data);
            return;
        }
        $pixel = "52494646 1a000000 57454250 5650384c 0d000000 2f000100 00000000 10002000 20000000";
        self::setData($dir, $photos, $pixel);
    }

    # 1.1.1
    public static function existsImgInLocal($dir, $photos): bool
    {
        $abs_path_dir = self::absPathDir($dir);
        $webpFileName = pathinfo($photos, PATHINFO_FILENAME) . '.webp';
        return file_exists("$abs_path_dir/$webpFileName") ? true : false;
    }

    # 1.1.1.1
	public static function absPathDir($dir)
	{    
        return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $dir;
    }

    # 1.1.2
	public static function existsImgInRemote($url): bool
	{    
        $headers = get_headers($url, 1);
        return strpos($headers[0], '200') ? true : false;
    }

    # 1.1.3
	public static function setWebp($dir, $photos, $data)
	{                
        $image = imagecreatefromstring($data);
        if ($image === false) {
            return;
        }
        self::mkDir($dir);
        $abs_path_dir = self::absPathDir($dir);
        $webpFileName = pathinfo($photos, PATHINFO_FILENAME) . '.webp';
        imagewebp($image, "$abs_path_dir/$webpFileName");
        imagedestroy($image);
    }

    # 1.1.4
	public static function setData($dir, $photos, $data)
	{    
        if (self::existsImgInLocal($dir, $photos)) {
            return;
        }
        self::mkDir($dir);
        $abs_path_dir = self::absPathDir($dir);
        $webpFileName = pathinfo($photos, PATHINFO_FILENAME) . '.webp';
        file_put_contents("$abs_path_dir/$webpFileName", $data);
    }

    # 1.1.4.1
	public static function mkDir($dir)
	{    
        $abs_path_dir = self::absPathDir($dir);
        if (file_exists($abs_path_dir)) {
            return;
        }
        mkdir($abs_path_dir, 0755, true);
    }
}

// Agregar soporte para AVIF en PHP
if (!function_exists('imagecreatefromavif')) {
    function imagecreatefromavif($filename) {
        // Agrega soporte para AVIF si tu versión de GD lo permite
        if (function_exists('imagecreatefromstring')) {
            $avif_data = file_get_contents($filename);
            return imagecreatefromstring($avif_data);
        }
        return false;
    }
}
