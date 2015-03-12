<?php
/**
 * 定义读取和解析 json 格式数据的类
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/utils.php';
require_once dirname(__FILE__) . '/FileReader.php';

/**
 * 读取和解析 json 格式数据的类
 */
class JsonFileReader extends FileReader {
    
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

    /**
     * 文件行数数组
     * @var int
    */
    private $arrLineCountList;

    /**
     * 当前行号
     * @var int
    */
    private $intCurrentLineno;
    /**
     * 存在 diff 的行号数组
     * @var array
    */
    private $arrDiffLineno;
    /**
     * 存在 diff 的行数
     * @var int
    */
    private $intDiffLinesCount;

    function __construct() {

        $this->arrJsonList       = array();
        $this->arrFilePaths      = array();
        $this->arrLineCountList  = array();
        $this->intCurrentLineno  = 0;
        $this->intDiffsCount     = 0;
        $this->intDiffLinesCount = 0;
        $this->arrDiffLineno     = array();

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
                $arrJson = explode("\n", $strContents);
                array_pop($arrJson);
                array_push($this->arrJsonList, $arrJson);
                array_push($this->arrLineCountList, count($arrJson));
            } catch (Exception $e) {
                return $e->getCode();
            }

        }
        
        return 0;

    }

    /**
     * 获得下一组待比较的 json 串
     * @param null
     * @return array, 成功返回 json 串数组, 失败则返回空
    */
    public function getNextGroup() {
        $this->intCurrentLineno ++;
        $ret = array();

        try {
	        $count = count($this->arrJsonList);
            for ($i = 0; $i < $count; $i++) {
                $strJson = array_shift($this->arrJsonList[$i]);
                array_push($ret, $strJson);
            }
        } catch (Exception $e) {
            return null;
        }
        
        return $ret;

    }

    /**
     * 存储当前的 diff 详情
     * @param string, 当前 diff 详情
     * @return int, 成功返回 0，失败返回错误码
    */
    public function storeCurrentDiff($strDiffContent) {

        try {
            if (!in_array($this->intCurrentLineno, $this->arrDiffLineno)) {
                array_push($this->arrDiffLineno, $this->intCurrentLineno);
                $this->intDiffLinesCount ++;
            }
        } catch (Exception $e) {
            return $e->getCode();
        }

        $this->intDiffsCount ++;
        $strContent = '---line' . $this->intCurrentLineno . ':' . $strDiffContent;
        $this->strDiffResultBody .= $strContent;

        return 0;
    }

    /**
     * 定制最终的 diff 输出内容
     * @param null
     * @return int, 成功返回 0
    */
    protected function makeDiffResult() {
        $content = '';
        foreach ($this->arrFilePaths as $key => $filePath) {
            $content .= 'No.' . strval($key + 1) . ' file ' . $filePath . ' line no: ' . 
            $this->arrLineCountList[$key] . PHP_EOL;
        }

        $this->strDiffResultHeader = $content . 'there are ' . $this->intDiffsCount . 
        ' diffs[s] in ' . $this->intDiffLinesCount . ' line[s], next is the detail differences.' . 
        PHP_EOL . '+++++++++++++' . PHP_EOL;
        $this->strDiffResult = $this->strDiffResultHeader . $this->strDiffResultBody;

        return 0;
    }

}
