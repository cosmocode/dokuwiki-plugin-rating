<?php

/**
 *
 *
 * @author Michael Große <grosse@cosmocode.de>
 *
 * @group Michael Große <grosse@cosmocode.de>
 * @group plugin_rating
 * @group plugins
 */

class best_rating_test extends DokuWikiTest {
    protected $pluginsEnabled = array('rating');

    function test_vanilla_syntax_parsing() {
        $parser_response = p_get_instructions('{{rating}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'rating',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'startdate' => '',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{rating}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_date_syntax_parsing() {
        $parser_response = p_get_instructions('{{rating|startdate=2015-02-17}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'rating',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'startdate' => '2015-02-17',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{rating|startdate=2015-02-17}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_lang_syntax_parsing() {
        $parser_response = p_get_instructions('{{rating|lang=en}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'rating',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => 'en',
                        'startdate' => '',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{rating|lang=en}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_datelang_syntax_parsing() {
        $parser_response = p_get_instructions('{{rating|startdate=2015-02-17,lang=en}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'rating',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => 'en',
                        'startdate' => '2015-02-17',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{rating|startdate=2015-02-17,lang=en}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

}
