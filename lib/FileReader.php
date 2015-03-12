<?php
/**
 * 定义读取和解析文件的基类
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/utils.php';

/**
 * 读取和解析文件的基类
*/
abstract class FileReader {
    /**
     * 最终要输出到文件的 diff 结果
     * @var string
    */
    protected $strDiffResult;
    /**
     * diff 头内容，包含本次 diff 的统计信息
     * @var string
    */
    protected $strDiffResultHeader;
    /**
     * diff 数量，包含本次 diff 的不同数目
     * @var int
    */
    protected $intDiffsCount;
    /**
     * diff 详细内容，包含本次 diff 的细节内容
     * @var string
    */
    protected $strDiffResultBody;

    /**
     * 获取 strDiffResultBody 值
     * @param null
     * @return strDiffResultBody
    */
    public function getDiffResultBody() {

        return $this->strDiffResultBody;

    }

    /**
     * 获取 strDiffResult 值
     * @param null
     * @return strDiffResult
    */
    public function getDiffResult() {

        return $this->strDiffResult;

    }

    /**
     * 获取 intDiffsCount 值
     * @param null
     * @return intDiffsCount
    */
    public function getDiffsCount() {

        return $this->intDiffsCount;

    }

    /**
     * 以字符串形式返回文件内容
     * @param string, 文件路径
     * @param string, 文件编码格式 
     * @return string, 成功返回内容字符串，失败返回空
    */
    public function readFile($strFilePath, $strEncodeType = 'utf8') {

        if (!file_exists($strFilePath)) {
            Utils::printError("$strFilePath not exists");
            return null;
        }
        if (!is_readable($strFilePath)) {
            Utils::printError("$strFilePath not readable");
            return null;
        }

        try {
            $strContents = file_get_contents($strFilePath);
            if ('gbk' === $strEncodeType) {
                $strContents = mb_convert_encoding($strContents, 'utf8', 'gbk');
            }
        } catch (Exception $e) {
            return null;
        }

        return $strContents;

    }

    /**
     * 子类可以重写该函数，对 diff 的最终输出做定制 
	 * @param null
	 * @return int, 成功返回 0
    */
    protected function makeDiffResult() {

        echo 'override this method and do some customizing processes' . PHP_EOL;

        return 0;

    }

    /**
     * 将最终 diff 内容写入文件
     * @param string, 输出文件的路径
     * @return int, 成功返回 0 ，失败返回错误码
    */
    public function flushDiffToFile($strOutputFile) {
        try {
            $this->makeDiffResult();    
            file_put_contents($strOutputFile, $this->strDiffResult);
        } catch (Exception $e) {
            //echo "erro code: " . $e->getCode() . PHP_EOL;
            return $e->getCode();
        }

        return 0;

    }
}
