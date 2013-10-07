<?php
/**
 * Smarty image crop plugin
 * Type:     plugin
 * Name:     smarty_function_image_crop
 * Purpose:  generate image thumb
 *
 * @param array
 * @author gyani
 * @examples
 *{html_image  url="image_path" alt="alt tag" class="example"}
 */

//include codeigniter image library files
require_once('Image_lib.php');
require_once('MY_Image_lib.php');

//define Constants
define("ROOT_URL","http://www.example.com"); //your domain name (your root url)
define("GENERATE_THUMB_IMAGE_PATH","/var/www/my_project/thumb_image"); //folder where thumb image will store


//function for generate image thumb
function smarty_function_image_crop($params){

    if(empty($params['src'])){
        return "please provide image src";
    }
    $width='';
    $height='';
    $classes= array();
    $extra='';
    $errorMessage= '';

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'src':
                break;
            case 'height':
                break;
            case 'width':
                break;
            case 'id':
                break;
            case 'maintain_ratio':
                break;
            case 'class':
                $classes[] = $_val;
                break;
            case 'alt':
                if(!is_array($_val))
                    $$_key = $_val;
                else
                    $errorMessage= "extra attribute '$_key' cannot be an array";
                break;
            default:
                if(!is_array($_val))
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                else
                    $errorMessage= "extra attribute '$_key' cannot be an array";
                break;
        }
    }

    if(!empty($errorMessage)){
        return $errorMessage;
    }

    if(!empty($params['width'])){
        $width= $params['width'];
    }
    if(!empty($params['height'])){
        $height=$params['height'];
    }

    if(!empty($params['class'])){
        $class_str = 'class="'.join(' ', $classes).'"';
    }

    if(!empty($params['id'])) {
        $id=$params['id'];
        $id_str = " id=\"$id\" ";
    }

    if(!empty($alt)){
        $alt_str = " alt=\"$alt\" ";
    }

    $params['src']=trim($params['src']);
    $_image_path = $params['src'];

    //create unique name for new thumb image
    $pathParts = array(
        basename($_image_path),
        intval($width).'x'.intval($height),
        filemtime($_image_path),
    );

    //thumb image name ::gyani do this proper way
    $cache_extend_path = preg_replace('/[^(\x20-\x7F)]*/','', str_replace('_', '-', join('-', $pathParts)) . '.' . pathinfo($_image_path, PATHINFO_EXTENSION));

    //take image  first two name for folder
    $folder = strtolower(substr($cache_extend_path, 0, 2));

    //create folder for generated image
    $cache_extend_path = \hiq\util\Factory::makePath($folder, $cache_extend_path);

    //new created image path
    $cache_image = \hiq\util\Factory::makePath(\hiq\util\Factory::getConstant('DIRPATH__RESOURCES__IMAGES__GENERATED'), $cache_extend_path);


    $urlHost = \hiq\util\Factory::getConstant('CDNURL__IMAGES__GENERATED'); //host name

    //this is for generate image thumb only once
    if(!file_exists($cache_image) || !@filesize($cache_image)) { //if image thumb is not unable

        $dir = pathinfo($cache_image, PATHINFO_DIRNAME);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $configs = array('source_image' => $_image_path, 'new_image' => $cache_image, 'width' => $params['width'], 'height' => $params['height'], 'maintain_ratio' => false);
        $image= new MY_Image_lib();
        $image->set_enlarge(TRUE);
        $image->thumb($configs);
    }

    $path = $urlHost . $cache_extend_path;
    return '<img src="'.$path.'" '.$alt_str.'width="'.$width.'" height="'. $height .'"'. $id_str . $class_str .' '.$extra.'/>';

}