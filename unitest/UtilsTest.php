<?php
/**
* 定义进行工具函数单测的类
* @author zangzhe
*/

$UtilesTestDir = dirname(__FILE__);

require_once $UtilesTestDir . '/../lib/JsonFileReader.php';
require_once $UtilesTestDir . '/../lib/XmlFileReader.php';
require_once $UtilesTestDir . '/../lib/utils.php';
require_once $UtilesTestDir . '/../lib/DiffTool.php';

/**
* 进行工具函数单测的类
*/
class UtilesTest extends PHPUnit_Framework_TestCase {
    
    /**
     * 读取和解析文件的对象
     * @var object
    */
    protected $reader;
    
    /**
     * 单元测试初始化处理 
    */
    protected function setUp() {
    
        $this->reader = new JsonFileReader();
        $arrFiles = array('./resource/file1.json', './resource/file2.json');
        $this->reader->loadFiles($arrFiles); 

    }
    
    /**
     * utils 函数 check_input 单元测试 
     * @condition case 1: 输入缺少选项
     * @expect    case 1: 异常输出相匹配，并返回错误码 -2
     * @condition case 2: 输入错误 -t 选项值
     * @expect    case 2: 返回错误码 -3
     * @condition case 3: 输入空选项
     * @expect    case 3: 返回错误码 -3
     * @condition case 4: 输入错误 -e 选项值
     * @expect    case 4: 返回错误码 -3
     * @condition case 5: 输入正确选项
     * @expect    case 5: 返回 0 
    */
    public function testCheckInput() {

        // case 1
        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
        );
        $outputRegex = '.*Invalid arguments, pls check input.*';
        $this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = Utils::checkInput($options);
        $this->assertEquals(-2, $ret, 'bad option not return -2');  

        // case 2
        $options = array( 
            't' => 'notype',
            'f' => 'firstFile secondFile',
            'o' => 'out',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret, 'bad -t option value not return -3');    
        
        // case 3
        $options = array( 
            't' => 'json',
            'f' => '',
            'o' => 'out',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret, 'null option value not return -3');    

        // case 4
        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
            'o' => 'out',
            'e' => 'notype',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret, 'bad -e option value not return -3');  

        // case 5
        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
            'o' => 'out',
            'e' => 'gbk',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(0, $ret, 'good option not return 0');            

    }

    /**
     * utils 函数 diff 单元测试 
     * @condition 输入 reader
     * @expect    函数返回值为 0
    */
    public function testDiff() {
        $diff = new DiffTool();
        $ret = $diff->diff($this->reader);
        $this->assertEquals(0, $ret, 'diff method not return 0');            

    }
    
}

?>
