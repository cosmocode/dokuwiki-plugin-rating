<?php
/**
 * DokuWiki Plugin rating (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_rating extends DokuWiki_Plugin {

    /** @var helper_plugin_sqlite */
    protected $sqlite = null;

    /**
     * initializes the DB connection
     *
     * @return helper_plugin_sqlite|null
     */
    public function getDBHelper() {
        if(!is_null($this->sqlite)) return $this->sqlite;

        $this->sqlite = plugin_load('helper', 'sqlite');
        if(!$this->sqlite) {
            msg('The rating plugin requires the sqlite plugin', -1);
            $this->sqlite = null;
            return null;
        }

        $ok = $this->sqlite->init('rating', __DIR__ . '/db');
        if(!$ok) {
            msg('rating plugin sqlite initialization failed', -1);
            $this->sqlite = null;
            return null;
        }

        return $this->sqlite;
    }

    /**
     * Current user identifier
     *
     * @return string
     */
    public function userID() {
        if(isset($_SERVER['REMOTE_USER'])) return $_SERVER['REMOTE_USER'];
        return clientIP(true);
    }

    /**
     * Display the rating tool in a template
     *
     * @param bool $inner used for AJAX updates
     * @param bool $print Should the HTML be printed or returned?
     * @return null|string
     */
    public function tpl($inner = false, $print = true) {
        global $ID;

        $sqlite = $this->getDBHelper();
        if(!$sqlite) return;

        $sql     = "SELECT sum(value) FROM ratings WHERE page = ?";
        $res     = $sqlite->query($sql, $ID);
        $current = (int) $sqlite->res2single($res);
        $sqlite->res_close($res);

        $sql  = "SELECT value FROM ratings WHERE page = ? AND rater = ?";
        $res  = $sqlite->query($sql, $ID, $this->userID());
        $self = (int) $sqlite->res2single($res);
        $sqlite->res_close($res);

        $ret = '';
        
        if(!$inner) $ret .= '<div class="plugin_rating">';
        $ret .= '<span class="intro">' . $this->getLang('intro') . '</span>';

        $class = ($self == -1) ? 'act' : '';
        $ret .= '<a href="' . wl($ID, array('rating' => -1)) . '" class="plugin_rating_down ' . $class . ' plugin_feedback" data-rating="-1">-1</a>';
        $ret .= '<span class="current">' . $current . '</span>';

        $class = ($self == 1) ? 'act' : '';
        $ret .= '<a href="' . wl($ID, array('rating' => +1)) . '" class="plugin_rating_up ' . $class . '" data-rating="1">+1</a>';

        if(!$inner) $ret .= '</div>';
        
        if($print) echo $ret;
        return $ret;
    }

    /**
     * Removes all entries for a page
     *
     * @param $page
     */
    public function remove($page) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return;

        $sql = "DELETE FROM ratings WHERE page = ?";
        $res  = $sqlite->query($sql, $page);
        $sqlite->res_close($res);
    }

    /**
     * Get the best voted pages
     *
     * @param int $num
     * @return array
     */
    public function best($lang, $startdate, $num = 10) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return array();

        $sqlbegin  = "SELECT sum(value) as val, page, lang FROM ratings ";
        $sqlend = "GROUP BY page ORDER BY sum(value) DESC LIMIT ?";
        if ($lang === null && $startdate === null){
            $sql = $sqlbegin . $sqlend;
            $res  = $sqlite->query($sql, $num);
        } else if ($lang !== null && $startdate === null) {
            $sql = $sqlbegin . "WHERE lang = ? " . $sqlend;
            $res  = $sqlite->query($sql, $lang, $num);
        } else if ($lang === null && $startdate !== null){
            $sql = $sqlbegin . "WHERE date >= ? " . $sqlend;
            $res  = $sqlite->query($sql, $startdate, $num);
        } else {
            $sql = $sqlbegin . "WHERE lang = ? AND date >= ? " . $sqlend;
            $res  = $sqlite->query($sql, $lang, $startdate, $num);
        }
        $list = $sqlite->res2arr($res);
        $sqlite->res_close($res);
        return $list;
    }

    /**
     * Store a rating
     *
     * @param int $rate either -1 or +1
     * @param string $page page to rate
     */
    public function rate($rate, $page) {
        if($rate < -1) $rate = -1;
        if($rate > 1) $rate = 1;

        $sqlite = $this->getDBHelper();
        if(!$sqlite) return;

        // ignore any bot accesses
        if(!class_exists('Jaybizzle\CrawlerDetect\CrawlerDetect')){
            require (__DIR__ . '/CrawlerDetect.php');
        }
        $CrawlerDetect = new Jaybizzle\CrawlerDetect\CrawlerDetect();
        if($CrawlerDetect->isCrawler()) return;

        $translation = plugin_load('helper', 'translation');
        if (!$translation) {
            $lang = '';
        } else {
            $lang = $translation->getLangPart($page);
        }

        $date = date('Y-m-d');

        $sql = "INSERT OR REPLACE INTO ratings (page, rater, lang, date, value) VALUES (?, ?, ?, ?, ?)";
        $sqlite->query($sql, $page, $this->userID(), $lang, $date, $rate);
    }
}

// vim:ts=4:sw=4:et:
