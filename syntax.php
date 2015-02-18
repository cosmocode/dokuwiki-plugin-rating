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
        return 'protected';
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
        $this->Lexer->addEntryPattern('\\{\\{rating\\|?(?=.*?\\}\\})', $mode, 'plugin_rating');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('.*?\\}\\}', 'plugin_rating');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {
        if ($state==DOKU_LEXER_EXIT) {
            $options = array('lang' => null, 'startdate' => null );
            $match = rtrim($match,'\}');
            if ($match != '') {
                $match = explode(",", $match);
                foreach($match as $option) {
                    $options[explode('=', $option)[0]] = explode('=', $option)[1];
                }
            }
            return array($state, $options);
        } else {
            return array($state, '');
        }
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        if($format == 'metadata') return false;
        if($data[0] != DOKU_LEXER_EXIT) return false;
        /** @var helper_plugin_rating $hlp */
        $hlp  = plugin_load('helper', 'rating');
        $list = $hlp->best($data[1]['lang'],$data[1]['startdate'], 20);

        $renderer->listo_open();
        $num_items=0;
        foreach($list as $item) {
            if (auth_quickaclcheck($item['page']) < AUTH_READ) continue;
            $num_items = $num_items +1;
            $renderer->listitem_open(1);
            if (strpos($item['page'],':') === false) {
                $item['page'] = ':' . $item['page'];
            }
            $renderer->internallink($item['page']);
            $renderer->cdata(' (' . $item['val'] . ')');

            $renderer->listitem_close();
            if ($num_items >= 10) break;
        }
        $renderer->listo_close();
    }

}
