<?php

namespace nextrip\helpers;

class File {

    /**
     * 抓取文件
     * @param string $url 文件路径
     * @param integer $tryCount 尝试次数 默认为10次
     * @param integer $timeout 每次抓取超时秒数 默认为10秒
     * @return string 文件内容 抓取不到将返回空
     */
    public static function grabFile($url, $tryCount = 3, $timeout = 10) {
        $file = null;
        for ($i = 0; $i < $tryCount; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);   //只需要设置一个秒的数量就可以  
            $file = curl_exec($ch);
            if ($file) {
                break;
            }
        }
        return $file;
    }
    
    /**
     * 抓取文件 并保存
     * @param string $url URL路径
     * @param string $filePath 保存文件路径
     * @param integer $tryCount 尝试次数 默认为10次
     * @param integer $timeout 每次抓取超时秒数 默认为10秒
     * @return string 文件内容 抓取不到将返回空
     */
    public static function grabFileAndSave($url, $filePath, $dirMode=0777, $tryCount = 3, $timeout = 10) {
        $file = null;
        for ($i = 0; $i < $tryCount; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);   //只需要设置一个秒的数量就可以  
            $file = curl_exec($ch);
            if ($file) {
                break;
            }
        }
        if($file) {
            mkdirs(dirname($filePath),$dirMode);
            file_put_contents($filePath, $file);
        }
        return $file;
    }

    /**
     * 抓取远程图片 并生成到指定的路径 暂时只支持JPG格式的图片,其他格式会返回false
     * @param string $url 远程图片的路径
     * @param string $saveFile 保存到文件 如果为空 表示不需要保存到文件
     * @param string $allowExt 允许的图片扩展名 all 为不限制
     * @param integer $tryCount 最多尝试次数 默认为2次
     * @param integer $timeout 超时时间 默认为10秒
     * @return mix 如果获取成功  返回照片流 或 false
     */
    public static function grabImage($url, $saveFile, $allowExt = 'all', $tryCount = 2, $timeout=10) {
        if (!$url || ($allowExt != 'all' && (strtolower(strrchr($url, '.')) != $allowExt))) {
            return false;
        }
        while ($tryCount > 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $img = curl_exec($ch);
            $tryCount--;
            if ($img) {
                break;
            }
        }
        if ($img) {
            if ($saveFile) {
                $fp = fopen($saveFile, "a");
                fwrite($fp, $img);
                fclose($fp);
            }
            return $img;
        } else {
            return false;
        }
    }

    /**
     * 获取上传文件错误
     * @return 无错误时返回false 或 错误内容
     */
    public static function getUploadError($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $error = '文件太大(不能超过4M)';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = '文件太大(不能超过4M)';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = '服务器只接收到了部分文件';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = '没有上传文件';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = '没有临时目录接收该文件';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = '系统不能写入该文件';
                break;
            case UPLOAD_ERR_EXTENSION:
                $error = 'PHP扩展错误,上传被中断';
                break;
            case UPLOAD_ERR_OK:
                $error = FALSE;
                break;
            default :
                $error = '未知错误，请重试';
        }

        return $error;
    }

}
