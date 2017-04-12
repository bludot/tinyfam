<?php

namespace Core;

class HTML {
    private $js = array();

    private $loader;
    public $twig;

    function __construct() {
        $this->loader = new \Twig_Loader_Filesystem(ROOT . DS . 'application/views');
        $this->twig = new \Twig_Environment($this->loader, array(
            //'cache' => ROOT . DS . '/tmp/compilation_cache',
            'cache' => false,
            'debug' => true,
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    function shortenUrls($data) {
        $data = preg_replace_callback('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', array(get_class($this), '_fetchTinyUrl'), $data);
        return $data;
    }

    private function _fetchTinyUrl($url) { 
        $ch = curl_init(); 
        $timeout = 5; 
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url[0]); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
        $data = curl_exec($ch); 
        curl_close($ch); 
        return '<a href="'.$data.'" target = "_blank" >'.$data.'</a>'; 
    }

    function sanitize($data) {
        return mysql_real_escape_string($data);
    }

    function link($text,$path,$prompt = null,$confirmMessage = "Are you sure?") {
        $path = str_replace(' ','-',$path);
        if ($prompt) {
            $data = '<a href="javascript:void(0);" onclick="javascript:jumpTo(\''.BASE_PATH.'/'.$path.'\',\''.$confirmMessage.'\')">'.$text.'</a>';
        } else {
            $data = '<a href="'.BASE_PATH.'/'.$path.'">'.$text.'</a>';  
        }
        return $data;
    }

    function includeJs($fileName) {
        $data = '<script src="'.BASE_PATH.'/js/'.$fileName.'.js"></script>';
        return $data;
    }

    function includeCss($fileName) {
        $data = '<style href="'.BASE_PATH.'/css/'.$fileName.'.css"></script>';
        return $data;
    }
}
