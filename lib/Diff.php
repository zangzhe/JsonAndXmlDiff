<?php
/**
 * DiffTool 类定义文件
 * @author zangzhe
 */

require_once dirname(__FILE__) . '/JsonFileReader.php';
require_once dirname(__FILE__) . '/XmlFileReader.php';

/**
 * diff 主功能类
 */
class DiffTool {

    /**
     * 将多维数组转换为一维数组，其中 key 为节点路径，value 为叶子节点值。
     * @param array
     * @param string
     * @return 解析成功返回前层的数组，错误则返回空
    */
    private function parseArray($leaf, $path = null) {

        $paths = array();
        if (is_array($leaf)) {
            foreach ($leaf as $key => $value) {
                if (is_int($key)) {
                    $key = '[' . $key. ']';
                }
                $innerArray = $this->parseArray($value, (null === $path) ? ($key) : 
                    ($path . '->' . $key));
                if (!is_array($innerArray)) {
                    return null;
                }
                foreach ($innerArray as $key => $val) {
                    $paths[$key] = $val;
                }
            }
        } else if (is_scalar($leaf)) {
            $paths[$path] = $leaf;
        } else {
            return null;
        }

        return $paths;

    }

    /**
     * diff功能主函数
     * @param FileReader
     * @return int, 执行正确返回 0，错误返回错误码
    */
    public function diff(&$reader) {
        while (1) {
            $arrDataStrList = $reader->getNextGroup();
            //var_dump($arrDataStrList);

            if (!is_array($arrDataStrList)) {
                Utils::printError('load file error');
                return -1;
            }

            $arrParsedLineList = array();
            $continue = false;
            foreach ($arrDataStrList as $key => $strDataStr) {
                if (null === $strDataStr) {
                    Utils::printInfo('parse finish');
                    break 2;
                }
                $arrDataArr = json_decode($strDataStr, true);
                if (null === $arrDataArr) {
                    $reader->storeCurrentDiff('No.' . strval($key+1) . ' has bad data' . PHP_EOL);
                    $continue = true;
                    break;    
                }
                $arrParsedLine = $this->parseArray($arrDataArr);    
                if (!is_array($arrParsedLine)) {
                    Utils::printError('parse data error');
                    $continue = true;
                    break;
                }
                array_push($arrParsedLineList, $arrParsedLine);
            }

            if ($continue) {
                continue;
            }
            //print_r($arrParsedLineList);
            $arrSameKeys = array_intersect_key($arrParsedLineList[0], 
                $arrParsedLineList[1]);
            $intGroupNum = count($arrParsedLineList);
            for ($i = 2; $i < $intGroupNum; $i ++) {
                $arrTmp = array_intersect_key($arrParsedLineList[$i], 
                    $arrSameKeys);
                $arrSameKeys = $arrTmp;
            } 
            //print_r($arrSameKeys);

            $arrDiffKeys = array();
            foreach ($arrParsedLineList as $arrParsedLine) {
                $arrTmp = array_diff_key($arrParsedLine, $arrSameKeys);
                //print_r($arrTmp);
                $arrDiffKeys = array_merge($arrDiffKeys, $arrTmp);
            }
            //print_r($arrDiffKeys);

            // 对不同 key 做 diff 判断
            foreach ($arrDiffKeys as $key => $val){
                //print_r($key);
                $strDiffContent = $key . PHP_EOL;
                foreach ($arrParsedLineList as $arrParsedLine) {
                 
                    $strDiffContent = $strDiffContent . "\t>" . (isset($arrParsedLine[$key]) ? ("\"" . 
                    $arrParsedLine[$key] . "\"") : ("")) . PHP_EOL;

                }
                //print_r($strDiffContent);
                $reader->storeCurrentDiff($strDiffContent);
            
            }

            // 对不同 value 做 diff 判断
            foreach ($arrSameKeys as $key => $val) {
                $print = false;
                foreach ($arrParsedLineList as $arrParsedLine) {
                    if (!isset($arrParsedLine[$key])) {
                        $print = false;
                        break;
                    }
                    if ($arrParsedLine[$key] === $val) {
                        continue;
                    }
                    $print = true;
                }
                
                if ($print) {
                    $strDiffContent = $key . PHP_EOL;
                    foreach ($arrParsedLineList as $arrParsedLine) {
                        $strDiffContent = $strDiffContent . "\t> " .
                    "\"" . $arrParsedLine[$key] . "\"" . PHP_EOL;
                    }
                    $reader->storeCurrentDiff($strDiffContent);
                }
            }
        }

        return 0;

    }   

}
