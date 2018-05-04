<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

class ForkController extends Controller{
    //php多线程
    public function actionFork(){
        $pid = pcntl_fork();
        if($pid == -1){
            //创建失败，退出
            die('创建失败');
        }else{
            if($pid){
                //从这里开始写的代码是父进程的,因为写的是系统程序,记得退出的时候给个返回值
                exit(0);
            }else{
                //从这里开始写的代码都是在新的进程里执行的,同样正常退出的话,最好也给一个返回值
                exit(1);
            }
        }
    }
}