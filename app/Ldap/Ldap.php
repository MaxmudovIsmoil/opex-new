<?php

namespace App\Ldap;
use Illuminate\Support\Facades\Log;

class Ldap
{
    protected $ldap_host;
    protected $ldap_port;
    protected $ldap_user;
    protected $ldap_password;
    protected $ldap_base_dn;
    protected $connection;


    public function __construct()
    {
        $this->ldap_host =  config('ldap.host');
        $this->ldap_port = config('ldap.port');
        $this->ldap_base_dn = config('ldap.base_dn');
        $this->ldap_user = config('ldap.user');
        $this->ldap_password = config('ldap.password');

        $this->connection = ldap_connect($this->ldap_host, $this->ldap_port);
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, 3);

    }


    public function handle(string $username, string $password): array
    {
        try {

            $admin = $this->auth($this->ldap_user, $this->ldap_password);

            $filter = '(samaccountname=' . $username . ')';
            $attributes = ['cn','samaccountname','mail','dn'];

            $result = ldap_search($this->connection, $this->ldap_base_dn, $filter, $attributes);
            $entries = ldap_get_entries($this->connection, $result);

            $user_dn  = isset($entries[0]['dn']) ? $entries[0]['dn'] : null;
            // $full_name = isset($entries[0]['cn'][0]) ? $entries[0]['cn'][0] : null;
            // $mail  = isset($entries[0]['mail'][0]) ? $entries[0]['mail'][0] : null;

            $user_auth = $this->auth($user_dn, $password);

            ldap_unbind($this->connection);


            if($user_dn == NULL)
                return  ['status' => false, 'error' => 'The username or password is incorrect'];


            return  ['status' => true, 'message' => __('Success logged in')];

        }
        catch (\Exception $exception) {
            return  ['status' => false, 'error' => 'The username or password is incorrect', 'message' => $exception->getMessage()];
        }

    }


    public function auth(string $user_dn, string $user_password)
    {
        $auth = ldap_bind($this->connection, $user_dn, $user_password);
        return $auth;
    }


    public function handle_confluence(string $username)
    {
        try {

            $this->auth($this->ldap_user, $this->ldap_password);

            $filter = '(samaccountname=' . $username . ')';
            $attributes = ['cn','samaccountname','mail','dn'];

            $result = ldap_search($this->connection, $this->ldap_base_dn, $filter, $attributes);
            $entries = ldap_get_entries($this->connection, $result);

            $mail = isset($entries[0]['mail'][0]) ? $entries[0]['mail'][0] : null;

            ldap_unbind($this->connection);


            if($mail == NULL)
                return ['status' => false, 'message' => __('User not found')];


            return ['status' => true, 'message' => __('Success logged in')];

        }
        catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }

    }

//    public function __destruct()
//    {
//        if ($this->connection) {
//            ldap_unbind($this->connection);
//        }
//    }

}
