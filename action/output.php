<?php
/**
 * DokuWiki Plugin struct (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr, Michael Große <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

use plugin\struct\meta\Assignments;
use plugin\struct\meta\SchemaData;

/**
 * Class action_plugin_struct_output
 *
 * This action component handles the automatic output of all schema data that has been assigned
 * to the current page by appending the appropriate instruction to the handler calls.
 *
 * The real output creation is done within the syntax component
 * @see syntax_plugin_struct_output
 */
class action_plugin_struct_output extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('PARSER_HANDLER_DONE', 'AFTER', $this, 'handle_output');

    }

    /**
     * Appends the instruction to render our syntax output component to each page
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function handle_output(Doku_Event &$event, $param) {
        global $ACT;
        if($ACT != 'show') return;

        $pos = 0; //FIXME this should probably be the file size?

        $event->data->calls[] = array(
            'plugin',
            array(
                'struct_output', array(), DOKU_LEXER_SPECIAL, ''
            ),
            $pos
        );
    }

}

// vim:ts=4:sw=4:et:
