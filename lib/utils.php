<?php
/**
 * 工具函数库文件
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/JsonFileReader.php';
require_once dirname(__FILE__) . '/XmlFileReader.php';

const G_VERSION = '0.0.3';


// 设置系统错误信息自定义捕获处理函数为 customError
set_error_handler('custom_error');

/**
 * 库函数错误、警告等捕获处理
 * @param null
*/
function custom_error($errno, $errstr) { 
    echo "[diff warning] $errstr" . PHP_EOL;
    throw new Exception($errstr);
}

/**
 * 工具类
 */
class Utils{
    /**
     * diff 错误打印函数
     * @param null
    */
    static public function printError($errstr) { 
        echo "[diff warning] $errstr" . PHP_EOL;
    }

    /**
     * diff 信息打印函数
     * @param null
    */
    static public function printInfo($infostr) { 
        echo "[diff info] $infostr" . PHP_EOL;
    }

    /**
     * 返回版本信息
     * @param null
     * @return string
    */
    static public function showVersion() {
        return "diff " . G_VERSION  . "\n";
    }

    /**
     * 返回帮助信息
     * @param null
     * @return string
    */
    static public function showHelp() {

        return "Usage: \tdiff:\tphp diff.php -t json -f 'file1 file2 file3' -o outFile [-e utf8]\n
        \thelp:\tphp diff.php -h\n
        \tversion:\tphp diff.php -v\n
        \t\t-t\t[json|xml] structure type of input files\n
        \t\t-f\tcompared files, separate by blank space, like 'file1.json file2.json'\n
        \t\t-o\tfile record the compared results\n
        \t\t-e\t[utf8|gbk] encoding type of input files, default is utf8\n
        \t\t-h\tprint help info\n
        \t\t-v\tprint version info\n";
    
    }

    /**
     * 检查用户输入参数
     * @param array
     * @return int, 输入正确返回 0，输入错误返回错误码
    */
    static public function checkInput($options) {
    
        $legalOptions = explode(',', 't,f,o');
        $wrongMsg = '';
        foreach ($legalOptions as $key) {
            if (!array_key_exists($key, $options)){
                $wrongMsg = 'Invalid arguments, pls check input.' . PHP_EOL;
                echo $wrongMsg;
                echo Utils::showHelp();
                return -2;
            }
        }

        foreach ($options as $key => $value) {
            switch ($key) {
                case 't':
                    if ('' === $value || ('json' !== $value && 'xml' !== $value)) {
                        $wrongMsg = '-t';
                    }
                    break;
                case 'f':
                    $files = explode(' ', $value);
                    foreach ($files as $file) {
                        if ('' === $file || count($files) < 2) {
                            $wrongMsg = '-f';
                            break;
                        }
                    }
                    break;
                case 'o':
                    if ('' === $value) {
                        $wrongMsg = '-o';
                    }
                    break;
                case 'e':
                    if ('utf8' !== $value && 'gbk' !== $value) {
                        $wrongMsg = '-e';
                    }
                    break;
                default:
                    break;
            }
        }

        if ('' !== $wrongMsg) {
            echo 'Invalid ' . $wrongMsg . ' argument, pls check input' . PHP_EOL;
            echo Utils::showHelp();
            return -3;
        }
        return 0;
    }
}
