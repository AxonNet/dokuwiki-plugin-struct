<?php
namespace plugin\struct\types;

use plugin\struct\meta\StructException;

class User extends AbstractBaseType {

    protected $config = array(
        'fullname' => true,
        'autocomplete' => array(
            'mininput' => 2,
            'maxresult' => 5,
        ),
    );

    /**
     * Autocompletion for user names
     *
     * @todo should we have any security mechanism? Currently everybody can look up users
     * @return array
     */
    public function handleAjax() {
        /** @var \DokuWiki_Auth_Plugin $auth */
        global $auth;
        global $INPUT;

        if(!$auth->canDo('getUsers')) {
            throw new StructException('The user backend can not search for users');
        }

        // check minimum length
        $lookup = trim($INPUT->str('search'));
        if(utf8_strlen($lookup) < $this->config['autocomplete']['mininput']) return array();

        // find users by login, fill up with names if wanted
        $max = $this->config['autocomplete']['maxresult'];
        $logins = (array) $auth->retrieveUsers(0, $max, array('user'=>$lookup));
        if((count($logins) < $max) && $this->config['fullname']) {
            $logins = array_merge($logins, (array) $auth->retrieveUsers(0, $max, array('name'=>$lookup)));
        }

        // clean up result
        $users = array();
        foreach($logins as $login => $info) {
            $users[$login] = $login.' - '.$info['name'];
        }

        return $users;
    }

}
