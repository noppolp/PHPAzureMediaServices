<?php
class RequestHeaders{
    public static $XMsVersion = 'x-ms-version';
    public static $XMsDate = 'x-ms-date';
    public static $XMsBlobType = 'x-ms-blob-type';
    public static $DataServiceVersion = 'DataServiceVersion';
    public static $MaxDataServiceVersion = 'MaxDataServiceVersion';
    public static $Authorization = 'Authorization';
    public static $Accept = 'Accept';
    public static $ContentType = 'Content-Type';
}

class RequestHeadersValues{
    public static $XMsVersion = '2.0';
    public static $DataServiceVersion = '3.0';
    public static $MaxDataServiceVersion = '3.0';
    public static $Authorization = 'Bearer %s';
    
    public static $BlobXMsVersion = '2011-08-18';
    public static $BlobXMsBlobType = 'BlockBlob';
    
    public static function GetBlobXMsDate(){
        return gmdate("D, d M Y H:i:s") . ' GMT'; 
        //return date('Y-m-d');
    }
}

class RequestContentType{
    public static $JSON = 'application/json;odata=verbose';
    public static $ATOM = 'application/atom+xml';
    public static $BLOB = 'application/octet-stream';
    public static $UTF8 = 'text/plain; charset=UTF-8';
}
?>