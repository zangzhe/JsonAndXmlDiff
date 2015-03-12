<?php
/**
* 定义进行工具函数单测的类
* @author zangzhe
*/

require_once dirname(__FILE__) . '/../lib/JsonFileReader.php';
require_once dirname(__FILE__) . '/../lib/XmlFileReader.php';
require_once dirname(__FILE__) . '/../lib/utils.php';
require_once dirname(__FILE__) . '/../lib/Diff.php';

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
    */
    public function testCheckInput() {

        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
        );
        $outputRegex = '.*Invalid arguments, pls check input.*';
        $this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = Utils::checkInput($options);
        $this->assertEquals(-2, $ret);  

        $options = array( 
            't' => 'notype',
            'f' => 'firstFile secondFile',
            'o' => 'out',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret);    
        
        $options = array( 
            't' => 'json',
            'f' => '',
            'o' => 'out',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret);    

        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
            'o' => 'out',
            'e' => 'notype',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(-3, $ret);  

        $options = array( 
            't' => 'json',
            'f' => 'firstFile secondFile',
            'o' => 'out',
            'e' => 'gbk',
        );
        $ret = Utils::checkInput($options);
        $this->assertEquals(0, $ret);            

    }

    // /**
    //  * utils 函数 parse_array 单元测试 
    // */
    // public function testParseArray() {

    //     $arrTest = json_decode("{\"format\":\"example\"}", true);
    //     $ret = parse_array($arrTest);
    //     $this->assertTrue(is_array($ret));

    // }

    /**
     * utils 函数 diff 单元测试 
    */
    public function testDiff() {
        $diff = new DiffTool();
        $ret = $diff->diff($this->reader);
        $this->assertEquals(0, $ret);            

    }
    
}

?>
