<?php

namespace plugin\struct\meta;

class Assignments {

    /** @var \helper_plugin_sqlite|null */
    protected $sqlite;

    /** @var  array All the assignments */
    protected $assignments;

    /**
     * Assignments constructor.
     */
    public function __construct() {
        /** @var \helper_plugin_struct_db $helper */
        $helper = plugin_load('helper', 'struct_db');
        $this->sqlite = $helper->getDB();

        if($this->sqlite) $this->load();
    }

    /**
     * Load existing assignments
     */
    protected function load() {
        $sql = 'SELECT * FROM schema_assignments ORDER BY assign';
        $res = $this->sqlite->query($sql);
        $this->assignments = $this->sqlite->res2arr($res);
        $this->sqlite->res_close($res);
    }

    /**
     * Returns a list of table names assigned to the given page
     *
     * @param string $page
     * @return string[] tables assigned
     */
    public function getPageAssignments($page) {
        $tables = array();

        $page = cleanID($page);
        $pns = ':' . getNS($page) . ':';

        foreach($this->assignments as $row) {
            $ass = $row['assign'];
            $tbl = $row['tbl'];

            $ans = ':' . cleanID($ass) . ':';

            if(substr($ass, -2) == '**') {
                // upper namespaces match
                if(strpos($pns, $ans) === 0) {
                    $tables[] = $tbl;
                }
            } else if(substr($ass, -1) == '*') {
                // namespaces match exact
                if($ans == $pns) {
                    $tables[] = $tbl;
                }
            } else {
                // exact match
                if(cleanID($ass) == $page) {
                    $tables[] = $tbl;
                }
            }
        }

        return array_unique($tables);
    }
}