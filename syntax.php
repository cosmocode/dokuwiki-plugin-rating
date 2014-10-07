<?php

/** DokuWiki Plugin rating (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_rating extends DokuWiki_Syntax_Plugin {

    /**
     * What kind of syntax are we?
     */
    function getType() {
        return 'substition';
    }

    /**
     * Where to sort in?
     */
    function getSort() {
        return 200;
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\\{\\{rating\\}\\}', $mode, 'plugin_rating');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {
        return array();
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        if($format == 'metadata') return false;
        /** @var helper_plugin_rating $hlp */
        $hlp  = plugin_load('helper', 'rating');
        $list = $hlp->best();

        $renderer->listu_open();
        foreach($list as $item) {
            $renderer->listitem_open(1);
            $renderer->internallink($item['page']);
            $renderer->listitem_close();
        }
        $renderer->listu_close();
    }

}