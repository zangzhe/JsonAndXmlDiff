<?php
/**
 * diff 工具主入口 
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/../lib/utils.php';
require_once dirname(__FILE__) . '/../lib/Diff.php';
require_once dirname(__FILE__) . '/../lib/JsonFileReader.php';
require_once dirname(__FILE__) . '/../lib/XmlFileReader.php';

// 校验输入参数
$g_options = getopt('t:f:o:e:hv');

if (array_key_exists('h', $g_options)) {
    echo Utils::showHelp();
    return 0;
}

if (array_key_exists('v', $g_options)) {
    echo Utils::showVersion();
    return 0;
}

if (0 !== Utils::checkInput($g_options)) {
	return -1;
}

// 全局变量赋值
$g_dataType   = $g_options['t'];
$g_arrFiles   = explode(' ', $g_options['f']); 
$g_outputFile = $g_options['o'];
$g_encodeType = 'utf8';
if (array_key_exists('e', $g_options)) {
    $g_encodeType = $g_options['e'];
}

$g_reader;
switch ($g_dataType) {
    case 'json':
        $g_reader = new JsonFileReader();
        break;
    case 'xml':
        $g_reader = new XmlFileReader();
        break;
    default:
        Utils::printError('invalid data type');
        return -1;
}

// 载入文本文件
$ret = $g_reader->loadFiles($g_arrFiles, $g_encodeType);	
if (0 !== $ret) {
    echo 'diff fail' . PHP_EOL;
    return -1;
}

// diff 主程序
$diff = new DiffTool();
$ret = $diff->diff($g_reader);
//$ret = diff($g_reader);
if (0 !== $ret) {
    echo 'diff fail' . PHP_EOL;
    return -1;
}

// 结果输出到文件
$ret = $g_reader->flushDiffToFile($g_outputFile);
if (0 !== $ret) {
    echo 'diff fail' . PHP_EOL;
    return -1;
}

echo 'diff finish' . PHP_EOL;
