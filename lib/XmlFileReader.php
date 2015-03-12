<?php
/**
 * 定义读取和解析 xml 格式文件的类
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/utils.php';
require_once dirname(__FILE__) . '/FileReader.php';

/**
 * 读取和解析 xml 格式文件的类
 */
class XmlFileReader extends FileReader {

    /**
     * 所有文件内容的 json 格式字符串二维数组
     * @var array
    */
    private $arrJsonList;

    /**
     * 文件名数组
     * @var array
    */
    private $arrFilePaths;

    function __construct() {
        $this->arrJsonList = array();
        $this->intDiffsCount = 0;

    }
    
    /**
     * 解析 xml 字符串
     * @param string, 文件内容 
     * @return string, 成功返回 json 字符串
    */
    private function parseXml($strContents) {
        $testXml = simplexml_load_string($strContents);
        $strXml = simplexml_load_string('<default>' . $strContents . '</default>');
        $strJson = json_encode($strXml);

        return $strJson;
    }

    /**
     * 读入并解析待比较文件
     * @param string, 左侧文件的路径
     * @param string, 右侧文件的路径
     * @param string, 文件编码格式 
     * @return int, 成功返回 0, 失败则返回错误码
    */
    public function loadFiles($arrFilePaths, $strEncodeType = 'utf8') {
        $this->arrFilePaths = $arrFilePaths;
        foreach ($arrFilePaths as $strFilePath) {
            $strContents = $this->readFile($strFilePath, $strEncodeType);
            if (null === $strContents) {
                return -1;
            }
        
            try {
                array_push($this->arrJsonList, $this->parseXml($strContents));    

            } catch (Exception $e) {
                return $e->getCode();
            }
        }

        return 0;

    }

    /**
     * 获得下一组待比较的 json 串
     * @param null
     * @return array, 成功返回 json 串数组, 失败返回空
    */
    public function getNextGroup() {
        //print_r($this->arrJsonList);
        $ret = array();
        try {
            foreach ($this->arrJsonList as $key => $strJson) {
                array_push($ret, $strJson);
                $this->arrJsonList[$key] = null;
            }
        } catch (Exception $e) {
            return null;
        }
        
        return $ret;
    }

    /**
     * 存储当前的 diff 详情
     * @param string, 当前 diff 详情
     * @return int, 成功返回 0
    */
    public function storeCurrentDiff($strDiffContent) {

        $this->intDiffsCount ++;
        $strContent = '---' . $strDiffContent;
        $this->strDiffResultBody .= $strContent;
        return 0;

    }

    /**
     * 定制最终的 diff 输出内容
     * @param null
     * @return int, 成功返回 0
    */
    protected function makeDiffResult() {

        $this->strDiffResultHeader = 'there are ' . $this->intDiffsCount .
        ' diffs[s] in the detail differences.' . PHP_EOL .
        '+++++++++++++' . PHP_EOL;

        $this->strDiffResult = $this->strDiffResultHeader . $this->strDiffResultBody;

        return 0;

    }

}
