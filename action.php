<?php
/**
 * DokuWiki Plugin rating (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_rating extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handle_ajax_call_unknown');
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'handle_vote');
        $controller->register_hook('IO_WIKIPAGE_WRITE', 'AFTER', $this, 'handle_delete');
    }



    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_delete(Doku_Event &$event, $param) {
        if($event->data[3]) return; // it's an old revision
        if($event->data[0][1]) return; // there's still content
        // still here? page was deleted

        /** @var helper_plugin_rating $hlp */
        $hlp = plugin_load('helper', 'rating');
        $hlp->remove($event->data[2]);
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_ajax_call_unknown(Doku_Event &$event, $param) {
        if($event->data != 'rating') return;
        $event->preventDefault();
        $event->stopPropagation();

        global $ID;
        $ID = getID();

        // let the other handler do it
        $this->handle_vote($event, $param);

        /** @var helper_plugin_rating $hlp */
        $hlp = plugin_load('helper', 'rating');
        $hlp->tpl(true);
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_vote(Doku_Event &$event, $param) {
        global $INPUT;
        global $ID;
        if(!$INPUT->has('rating')) return;

        $rate = $INPUT->int('rating');

        /** @var helper_plugin_rating $hlp */
        $hlp = plugin_load('helper', 'rating');
        $hlp->rate($rate, $ID);
    }

}

// vim:ts=4:sw=4:et:
