<?php
namespace app\components;

use Yii;
use yii\web\HttpException;

/**
 * Class for all main function
 */
class Globals {
    /**
     * For some reasong server side file_get_contents replace & with &amp; in path
     * @param path
     * @return string
     */
    public static function download($path) {
        $path = "http://www.google.sk";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $path);
        Yii::info(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        Yii::info(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        $return = curl_exec($curl);

        Yii::info(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $return;
    }
}
