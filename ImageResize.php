<?php

class ImageResize {
    private $newImageWidth = 0;
    private $newImageHeight = 0;
    private $resizeType;
    private $sourceImage;
    private $sourceWidth;
    private $sourceHeight;


    public function __construct($srcImg) {
        $this->sourceImage = $srcImg;
        $size = getimagesize($this->sourceImage);
        $this->sourceWidth = $size[0];
        $this->sourceHeight = $size[1];
    }

    public function setWidth($width) {
        $this->newImageWidth = $width;
    }

    public function setHeight($heigth) {
        $this->newImageHeight = $heigth;
    }

    public function setResizingType($type) {
        $this->resizeType = $type;
    }

    private function createImage($width, $height, $destImage) {
        $original = imagecreatefrompng($this->sourceImage);
        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $resized, $original,
            0, 0, 0, 0,
            $width, $height,
            $this->sourceWidth, $this->sourceHeight
            );
        imagepng($resized, $destImage);
        return $resized;
    }

    private function cropImage($imageToCrop, $cropWidth, $cropHeight, $destImage) {
        $img = imagecrop($imageToCrop, ['x' => 0, 'y' => 0, 'width' => $cropWidth, 'height' => $cropHeight]);
        imagepng($img, $destImage);
    }

    private function fit($destImage) {
            try {
                if ($this->newImageWidth == 0) {

                    $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                    $this->createImage($newWidth, $this->newImageHeight, $destImage);

                } else if ($this->newImageWidth < $this->newImageHeight) {

                    $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                    $this->createImage($this->newImageWidth, $newHeight, $destImage);

                } else if ($this->newImageHeight == 0) {

                    $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                    $this->createImage($this->newImageWidth, $newHeight, $destImage);

                } else if ($this->newImageHeight < $this->newImageWidth) {

                    $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                    $this->createImage($newWidth, $this->newImageHeight, $destImage);

                } else if ($this->newImageWidth == $this->newImageHeight) {

                    if ($this->sourceWidth > $this->sourceHeight) {

                        $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                        $this->createImage($this->newImageWidth, $newHeight, $destImage);

                    } else if ($this->sourceWidth < $this->sourceHeight) {

                        $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                        $this->createImage($newWidth, $this->newImageHeight, $destImage);

                    } else {
                        $this->createImage($this->newImageWidth, $this->newImageHeight, $destImage);
                    }
                }
                echo "Resize type fit " . $destImage;
            } catch (DivisionByZeroError $ex) {
                echo "One of the values (width or height) should be more than zero \n";
            }
    }
    
    private function force($destImage) {
        try {
            if ($this->newImageWidth == 0) {

                $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                $this->createImage($newWidth, $this->newImageHeight, $destImage);

            } else if ($this->newImageHeight == 0) {

                $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                $this->createImage($this->newImageWidth, $newHeight, $destImage);

            } else {
                $this->createImage($this->newImageWidth, $this->newImageHeight, $destImage);
            }
            echo "Resize type force " . $destImage;
        } catch (ValueError $ex) {
            echo "Width and Height should be more than zero \n";
        }
    }

    private function fill($destImage) {
        try {
            if ($this->newImageWidth == 0) {

                $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                $this->createImage($newWidth, $this->newImageHeight, $destImage);

            } else if ($this->newImageHeight == 0) {

                $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                $this->createImage($this->newImageWidth, $newHeight, $destImage);

            } else if ($this->newImageWidth > $this->newImageHeight) {

                $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                $imageToCrop = $this->createImage($this->newImageWidth, $newHeight, $destImage);
                $this->cropImage($imageToCrop, $this->newImageWidth, $this->newImageHeight, $destImage);

            } else if ($this->newImageHeight > $this->newImageWidth) {

                $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                $imageToCrop = $this->createImage($newWidth, $this->newImageHeight, $destImage);
                $this->cropImage($imageToCrop, $this->newImageWidth, $this->newImageHeight, $destImage);

            } else if ($this->newImageWidth == $this->newImageHeight) {

                if ($this->sourceWidth > $this->sourceHeight) {

                    $newWidth = $this->sourceWidth / ($this->sourceHeight / $this->newImageHeight);
                    $imageToCrop = $this->createImage($newWidth, $this->newImageHeight, $destImage);
                    $this->cropImage($imageToCrop, $this->newImageWidth, $this->newImageHeight, $destImage);

                } else if ($this->sourceHeight > $this->sourceWidth) {

                    $newHeight = $this->sourceHeight / ($this->sourceWidth / $this->newImageWidth);
                    $imageToCrop = $this->createImage($this->newImageWidth, $newHeight, $destImage);
                    $this->cropImage($imageToCrop, $this->newImageWidth, $this->newImageHeight, $destImage);

                } else {

                    $this->createImage($this->newImageWidth, $this->newImageHeight, $destImage);

                }
            }
            echo "Resize type fill " . $destImage;
        } catch (DivisionByZeroError $ex) {
            echo "One of the values (width or height) should be more than zero \n";
        }
    }

    public function process($destImage) {
        if ($this->resizeType == 'fit') {
            return $this->fit($destImage);
        }
        if ($this->resizeType == 'force') {
            return $this->force($destImage);
        }
        if ($this->resizeType == 'fill') {
            return $this->fill($destImage);
        }
    }
}

$image = new ImageResize('img/php.png');
$image->setWidth(253);
$image->setHeight(512);
$image->setResizingType('fit');
$image->process('img/resized.png');
?>