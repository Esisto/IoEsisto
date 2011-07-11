<?php
    define("CATEGORY","News");
    require_once("Zend/Loader.php");
    Zend_Loader::loadClass('Zend_Gdata_YouTube');

class youtubeManager {
    
    /*
    *Crea una variabile contenente il codice della form
    *@param myVideoEntry: un oggetto Zend_Gdata_YouTube_VideoEntry
    *
    *@return: $status contiene una stringa contenente lo stato dell'upload
    */
    function checkUploadStatus($myVideoEntry){
        $state = $myVideoEntry->getVideoState();
        if ($state)
          $status = 'Upload status for video ID ' . $videoEntry->getVideoId() . ' is ' . $state->getName() . ' - ' . $state->getText() . "\n";
        else 
            $status = "Not able to retrieve the video status information yet. " . "Please try again later.\n";
        
        return $status;
    }
    
    /*
    *Crea una variabile contenente il codice del videoPlayer
    *@param url: l'id del video di youtube
    *@param width: opzionale (standard: width=640, height=385)
    *se width viene specificata, height viene calcolata in modo proporzionale
    *@return: $palyer contiene una stringa contenente il codice del videoPlayer
    */
    function getVideoPlayer($url,$width = 640){
        if($width == 640)
            $height=385;
        else
            $height=$width * 0.8235;
        $url = self::getUrl($url);
        $player="<object width='$width' height='$height'>
                <param name='movie' value='$url'></param>
                <param name='allowFullScreen' value='true'></param>
                <param name='allowscriptaccess' value='always'></param>
                <embed src='$url' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='$width' height='$height'></embed>
                </object>";
        
        return $player;
    }
    
    
    function getVideoID($url) {  
        $url = parse_url($url, PHP_URL_QUERY);  
        parse_str($url, $arr);  
        return isset($arr['v']) ? $arr['v'] : false;  
    }
    
    
    function getUrl($videoID){
        $url= "http://www.youtube.com/v/" . $videoID . "";
        return $url;
    }
    
    /*
     *@deprecated
    */
    /*
    *Aggiunge metadata al video e li invia a youtube
    *@param myVideoEntry: un oggetto Zend_Gdata_YouTube_VideoEntry
    *@param Title: il titolo del video da inserire 
    *@param Description: la descrizione del video, di default  una stringa vuota
    *@param Category: categoria del video, deve essere una categoria di YouTube valida!
    *@param Tags: un array contenente i tags
    *@param nextUrl: l'url a cui deve tornare dopo l'elaborazione
    *
    *@return: lo stesso oggetto in ingresso ma con i metadati aggiornati
    */
    function addMetaData($myVideoEntry, $Title, $Description ="", $Category=CATEGORY, $Tags=""){
        $myVideoEntry->setVideoTitle($Title);
        $myVideoEntry->setVideoDescription($Description);
        $myVideoEntry->setVideoCategory($Category);
        $myVideoEntry->SetVideoTags($Tags);
     
        return $myVideoEntry;
    }
     
    /*
     *@deprecated
    */
    /*
    *Ottiene un array con token e url per il video da caricare
    *@param yt: l'oggetto Zend_Gdata_YouTube contenente i dati relativi all'autenticazione
    *@param myVideoEntry: un oggetto Zend_Gdata_YouTube_VideoEntry
    *
    *@return: l'array $tokenArray
    *$tokenArray['token']  il token per il caircamento
    *$tokenArray['url']  l'url del video
    */
    function getTokenArray($yt, $myVideoEntry){
        $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
        $tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);
        
        return $tokenArray;
    }
    
    /*
     *@deprecated
    */
    /*
    *Crea una variabile contenente il codice della form di upload
    *@param $tokenArray: l'array contenente il token e l'url per il caricamento del video
    *
    *@return: $form contiene come string il codice della form
    */
    function getUploadForm($tokenArray,$nextUrl){
        $tokenValue = $tokenArray['token'];
        $postUrl = $tokenArray['url'].'?nexturl='. $nextUrl;
        $form = '<form action="'. $postUrl .'"
                method="post" enctype="multipart/form-data">'. 
                '<input name="file" type="file"/>'. 
                '<input name="token" type="hidden" value="'. $tokenValue .'"/>'.
                '<input value="Upload Video File" type="submit" />'. 
                '</form>';
            
        return $form;
    }
    
    /*
    *@deprecated
    */
    function showMetaDataForm(){
        echo "<form id='uploadForm' action='' method='post'>
                Enter video title:<br><input size='50' name='videoTitle' type='text'><br>
                Enter video description:<br><textarea cols='50' name='videoDescription'></textarea><br>
                Select a category: <select name='videoCategory'>
                <option value='Autos'>Autos &amp; Vehicles</option>
                <option value='Music'>Music</option>
                <option value='Animals'>Pets &amp; Animals</option>
                <option value='Sports'>Sports</option>
                <option value='Travel'>Travel &amp; Events</option>
                <option value='Games'>Gadgets &amp; Games</option>
                <option value='Comedy'>Comedy</option>
                <option value='People'>People &amp; Blogs</option>
                <option value='News'>News &amp; Politics</option>
                <option value='Entertainment'>Entertainment</option>
                <option value='Education'>Education</option>
                <option value='Howto'>Howto &amp; Style</option>
                <option value='Nonprofit'>Nonprofit &amp; Activism</option>
                <option value='Tech'>Science &amp; Technology</option>
                </select><br>
                Enter some tags to describe your video <em>(separated by spaces)</em>:<br><input name='videoTags' type='text' size='50' value='video'><br>
                <input type='submit' value='Inserisci dati'>
                </form>";
        return;
    }
    
}
?>