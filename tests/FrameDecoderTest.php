<?php

class FrameDecoderTest extends PHPUnit_Framework_Testcase
{
    /**
     * System under test
     * @var \Mib\Component\WebSocket\FrameDecoder
     */
    private $decoder;

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        $this->decoder = $this->createDecoder();
    }

    /**
     * TearDown the test environment
     */
    public function tearDown()
    {
        unset($this->decoder);
        $this->decoder = null;
    }

    /**
     * @param $actual
     * @param $expected
     *
     * @dataProvider getValidDecodeData
     */

    public function testDecode($actual, $expected, $mask)
    {
        $frame = $this->decoder->decode($actual);

        $this->assertEquals($expected, $frame->getHeader()->__toString());
        $this->assertEquals($mask, $frame->getHeader()->getMaskKey());
    }

    /**
     * Returns a bunch of valid frame headers
     * @return array
     */
    public function getValidDecodeData()
    {
        return [
            [ chr(129) . chr(129) . "MSK1_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:1|MASK:1|PAYLOAD:1]', 'MSK1'],
            [ chr(130) . chr(130) . "MSK2_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:2|MASK:1|PAYLOAD:2]', 'MSK2'],
            [ chr(131) . chr(131) . "MSK3_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:3|MASK:1|PAYLOAD:3]', 'MSK3'],
            [ chr(132) . chr(132) . "MSK4_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:4|MASK:1|PAYLOAD:4]', 'MSK4'],
            [ chr(133) . chr(133) . "MSK5_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:5|MASK:1|PAYLOAD:5]', 'MSK5'],
            [ chr(134) . chr(134) . "MSK6_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:6|MASK:1|PAYLOAD:6]', 'MSK6'],
            [ chr(135) . chr(135) . "MSK7_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:7|MASK:1|PAYLOAD:7]', 'MSK7'],
            [ chr(136) . chr(136) . "MSK8_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:8|MASK:1|PAYLOAD:8]', 'MSK8'],
            [ chr(137) . chr(137) . "MSK9_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:9|MASK:1|PAYLOAD:9]', 'MSK9'],
            [ chr(138) . chr(138) . "MS10_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:10|MASK:1|PAYLOAD:10]', 'MS10'],
            [ chr(139) . chr(139) . "MS11_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:11|MASK:1|PAYLOAD:11]', 'MS11'],
            [ chr(140) . chr(140) . "MS12_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:12|MASK:1|PAYLOAD:12]', 'MS12'],
            [ chr(141) . chr(141) . "MS13_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:13|MASK:1|PAYLOAD:13]', 'MS13'],
            [ chr(142) . chr(142) . "MS14_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:14|MASK:1|PAYLOAD:14]', 'MS14'],
            [ chr(143) . chr(143) . "MS15_DATA_" , '[FIN:1|RSV1:0|RSV2:0|RSV3:0|OPCODE:15|MASK:1|PAYLOAD:15]', 'MS15'],
        ];
    }



    /**
     * @return \Mib\Component\WebSocket\FrameDecoder
     */
    private function createDecoder()
    {
        return new Mib\Component\WebSocket\FrameDecoder();
    }

}