<?php

/**
 * ISPConfig 3 API wrapper PHP
 * @author Pablo Medina <pablo.medina@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 */
class ISPConfigWS
{

    /**
     * Holds the SOAPclient object
     *
     * access protected;
     * @var \SOAPclient
     */
    private $webService;

    /**
     * Holds the SOAP session ID
     *
     * access protected;
     * @var integer
     */
    protected $sessionId;
    /**
     * Holds the ISPConfig login details
     *
     * access private;
     * @var array
     */
    private $config;

    /**
     * Holds the SOAP response
     *
     * access private;
     * @var mixed
     */
    private $response;

    /**
     * Holds the parameters used for SOAP requests
     *
     * access private;
     * @var array
     */
    private $params;


    /**
     *   Sets up \SoapClient connection.
     * @throws \SoapFault upon SOAP/WDSL error.
     */
    public function __construct(\SoapClient $soapClient)
    {
        $this->webService = $soapClient;
    }

    /**
     * Get the API ID
     *
     * @return string Returns "self"
     * @access public
     */

    public function getResponse()
    {
        if (is_soap_fault($this->response))
            return json_encode(array(
                    'error' => array(
                        'code'    => $this->response->faultcode,
                        'message' => $this->response->faultstring
                    )
                )
            );

        if (!is_array($this->response))
            return json_encode(array(
                    'result' => $this->response
                )
            );

        return json_encode($this->response);
    }

    /**
     * Alias for getResponse
     *
     * @return mixed
     * @access public
     */
    public function response()
    {
        return $this->getResponse();
    }

    /**
     * Alias for setParams
     * @return $this
     * @access public
     */
    public function with($params)
    {
        $this->setParams($params);
        return $this;
    }

    /**
     * Set the parameters used for SOAP calls
     *
     * @param Array $params
     * @internal param mixed $params
     * @access public
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Extracts a parameter from $params and remove it from $params array
     *
     * @param $param
     * @return mixed
     * @access private
     */
    private function extractParameter($param)
    {
        $parameter = isset($this->params[$param]) ? $this->params[$param] : FALSE;
        unset($this->params[$param]);
        return $parameter;
    }

    /**
     * Holds the SOAPclient, creating it if needed.
     *
     * @access private
     * @return SoapClient
     */
    private function ws()
    {
        if ( !$this->sessionId)
            $this->login();

        return $this->webService;
    }

    /**
     * @return $this
     */
    public function login()
    {
        $user            = $this->extractParameter('loginUser');
        $password        = $this->extractParameter('loginPass');
        $this->sessionId = $this->webService->login($user, $password);
        return $this;
    }


    /**
     * @return $this
     */
    public function addClient()
    {
        $reseller_id    = $this->extractParameter('reseller_id');
        $this->response = $this->ws()->client_add($this->sessionId, $reseller_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function changeClientPassword()
    {
        $client_id      = $this->extractParameter('client_id');
        $password       = $this->extractParameter('password');
        $this->response = $this->ws()->client_change_password($this->sessionId, $client_id, $password);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteClient()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->client_delete($this->sessionId, $client_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getClient()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->client_get($this->sessionId, $client_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientByUsername()
    {
        $username       = $this->extractParameter('username');
        $this->response = $this->ws()->client_get_by_username($this->sessionId, $username);
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientID()
    {
        $user_id        = $this->extractParameter('user_id');
        $this->response = $this->ws()->client_get_id($this->sessionId, $user_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientSites()
    {
        $user_id        = $this->extractParameter('user_id');
        $this->response = $this->ws()->client_get_sites_by_user($this->sessionId, $user_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientTemplates()
    {
        $user_id        = $this->extractParameter('user_id');
        $this->response = $this->ws()->client_templates_get_all($this->sessionId, $user_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateClient()
    {
        $client_id  = $this->extractParameter('client_id');
        $resellerId = $this->extractParameter('reseller_id');

        $this->response = $this->ws()->client_add($this->sessionId, $client_id, $resellerId, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addDnsRecord()
    {
        $client_id = $this->extractParameter('client_id');
        $call      = 'dns_' . $this->params['type'] . '_add';

        $this->response = $this->ws()->{$call}($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteDnsRecord()
    {
        $client_id      = $this->extractParameter('client_id');
        $call           = 'dns_' . $this->params['type'] . '_delete';
        $this->response = $this->ws()->{$call}($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDnsRecord()
    {
        $client_id = $this->extractParameter('client_id');
        $call      = 'dns_' . $this->params['type'] . '_get';

        $this->response = $this->ws()->$call($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateDnsRecord()
    {
        $client_id = $this->extractParameter('client_id');
        $call      = 'dns_' . $this->params['type'] . '_update';

        $this->response = $this->ws()->$call($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addDnsZone()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->dns_zone_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteDnsZone()
    {
        $zone_id        = $this->extractParameter('zone_id');
        $this->response = $this->ws()->dns_zone_delete($this->sessionId, $zone_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDnsZone()
    {
        $zone_id        = $this->extractParameter('zone_id');
        $this->response = $this->ws()->dns_zone_get($this->sessionId, $zone_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDnsZonesByUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $server_id      = $this->extractParameter('server_id');
        $this->response = $this->ws()->dns_zone_get_by_user($this->sessionId, $client_id, $server_id);
        return $this;

    }

    /**
     * @return $this
     */
    public function setDnsZoneStatus()
    {
        $zone_id = $this->extractParameter('zone_id');
        $status  = $this->extractParameter('status');

        $this->response = $this->ws()->dns_zone_set_status($this->sessionId, $zone_id, $status);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateDnsZone()
    {
        $client_id      = $this->extractParameter('client_id');
        $zone_id        = $this->extractParameter('zone_id');
        $this->response = $this->ws()->dns_zone_update($this->sessionId, $client_id, $zone_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addDnsDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->domains_domain_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteDnsDomain()
    {
        $domain_id      = $this->extractParameter('domain_id');
        $this->response = $this->ws()->domains_domain_delete($this->sessionId, $domain_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDnsDomain()
    {
        $domain_id      = $this->extractParameter('domain_id');
        $this->response = $this->ws()->domains_domain_get($this->sessionId, $domain_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getUserDnsDomains()
    {
        $user_id        = $this->extractParameter('user_id');
        $this->response = $this->ws()->domains_get_all_by_user($this->sessionId, $user_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailAlias()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_alias_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailAlias()
    {
        $primary_id     = $this->extractParameter('alias_id');
        $this->response = $this->ws()->mail_alias_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailAlias()
    {
        $primary_id     = $this->extractParameter('alias_id');
        $this->response = $this->ws()->mail_alias_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailAlias()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('alias_id');
        $this->response = $this->ws()->mail_alias_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailBlacklist()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_blacklist_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailBlacklist()
    {
        $primary_id     = $this->extractParameter('blacklist_id');
        $this->response = $this->ws()->mail_blacklist_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailBlacklist()
    {
        $primary_id     = $this->extractParameter('blacklist_id');
        $this->response = $this->ws()->mail_blacklist_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailBlacklist()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('blacklist_id');
        $this->response = $this->ws()->mail_blacklist_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailCatchall()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_catchall_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailCatchall()
    {
        $primary_id     = $this->extractParameter('catchall_id');
        $this->response = $this->ws()->mail_catchall_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailCatchall()
    {
        $primary_id     = $this->extractParameter('catchall_id');
        $this->response = $this->ws()->mail_catchall_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailCatchall()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('catchall_id');
        $this->response = $this->ws()->mail_catchall_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_domain_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailDomain()
    {
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->mail_domain_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailDomain()
    {
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->mail_domain_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailDomainByDomain()
    {
        $domain         = $this->extractParameter('domain_name');
        $this->response = $this->ws()->mail_domain_get_by_domain($this->sessionId, $domain);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->mail_domain_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailFetchMail()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_fetchmail_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailFetchmal()
    {
        $primary_id     = $this->extractParameter('fetchmail_id');
        $this->response = $this->ws()->mail_fetchmail_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailFetchmail()
    {
        $primary_id     = $this->extractParameter('fetchmail_id');
        $this->response = $this->ws()->mail_fetchmail_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailFetchmail()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('fetchmail_id');
        $this->response = $this->ws()->mail_fetchmail_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailForward()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_forward_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailForward()
    {
        $primary_id     = $this->extractParameter('forward_id');
        $this->response = $this->ws()->mail_forward_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailForward()
    {
        $primary_id     = $this->extractParameter('forward_id');
        $this->response = $this->ws()->mail_forward_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailForward()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('forward_id');
        $this->response = $this->ws()->mail_forward_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailinglist()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_mailinglist_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailinglist()
    {
        $primary_id     = $this->extractParameter('mailinglist_id');
        $this->response = $this->ws()->mail_mailinglist_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailinglist()
    {
        $primary_id     = $this->extractParameter('mailinglist_id');
        $this->response = $this->ws()->mail_mailinglist_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailinglist()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('mailinglist_id');
        $this->response = $this->ws()->mail_mailinglist_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailPolicy()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_policy_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailPolicy()
    {
        $primary_id     = $this->extractParameter('policy_id');
        $this->response = $this->ws()->mail_policy_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailPolicy()
    {
        $primary_id     = $this->extractParameter('policy_id');
        $this->response = $this->ws()->mail_policy_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailPolicy()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('policy_id');
        $this->response = $this->ws()->mail_policy_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addSpamfilterBlacklist()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_spamfilter_blacklist_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteSpamfilterBlacklist()
    {
        $primary_id     = $this->extractParameter('spamfilterblacklist_id');
        $this->response = $this->ws()->mail_spamfilter_blacklist_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getSpamfilterBlacklist()
    {
        $primary_id     = $this->extractParameter('spamfilterblacklist_id');
        $this->response = $this->ws()->mail_spamfilter_blacklist_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateSpamfilterBlacklist()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('spamfilterblacklist_id');
        $this->response = $this->ws()->mail_spamfilter_blacklist_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addSpamfilterUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_spamfilter_user_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteSpamfilterUser()
    {
        $primary_id     = $this->extractParameter('spamfilteruser_id');
        $this->response = $this->ws()->mail_spamfilter_user_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getSpamfilterUser()
    {
        $primary_id     = $this->extractParameter('spamfilteruser_id');
        $this->response = $this->ws()->mail_spamfilter_user_get($this->sessionId, $primary_id);
        return $this;
    }


    /**
     * @return $this
     */
    public function updateSpamfilterUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('spamfilteruser_id');
        $this->response = $this->ws()->mail_spamfilter_user_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addSpamfilterWhitelist()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_spamfilter_whitelist_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteSpamfilterWhitelist()
    {
        $primary_id     = $this->extractParameter('spamfilterwhitelist_id');
        $this->response = $this->ws()->mail_spamfilter_whitelist_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getSpamfilterWhitelist()
    {
        $primary_id     = $this->extractParameter('spamfilterwhitelist_id');
        $this->response = $this->ws()->mail_spamfilter_whitelist_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateSpamfilterWhitelist()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('spamfilterwhitelist_id');
        $this->response = $this->ws()->mail_spamfilter_whitelist_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailTransport()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_transport_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailTransport()
    {
        $primary_id     = $this->extractParameter('transport_id');
        $this->response = $this->ws()->mail_transport_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailTransport()
    {
        $primary_id     = $this->extractParameter('transport_id');
        $this->response = $this->ws()->mail_transport_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailTransport()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('transport_id');
        $this->response = $this->ws()->mail_transport_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailuser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_user_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailUser()
    {
        $primary_id     = $this->extractParameter('user_id');
        $this->response = $this->ws()->mail_user_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailUser()
    {
        $primary_id     = $this->extractParameter('user_id');
        $this->response = $this->ws()->mail_user_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('user_id');
        $this->response = $this->ws()->mail_user_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailUserFilter()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_user_filter_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailUserfilter()
    {
        $primary_id     = $this->extractParameter('userfilter_id');
        $this->response = $this->ws()->mail_user_filter_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailUserFilter()
    {
        $primary_id     = $this->extractParameter('userfilter_id');
        $this->response = $this->ws()->mail_user_filter_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailUserfilter()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('userfilter_id');
        $this->response = $this->ws()->mail_user_filter_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addMailWhitelist()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->mail_whitelist_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteMailWhitelist()
    {
        $primary_id     = $this->extractParameter('whitelist_id');
        $this->response = $this->ws()->mail_whitelist_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getMailWhitelist()
    {
        $primary_id     = $this->extractParameter('whitelist_id');
        $this->response = $this->ws()->mail_whitelist_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateMailWhitelist()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('whitelist_id');
        $this->response = $this->ws()->mail_whitelist_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function getServer()
    {
        $server_id      = $this->extractParameter('server_id');
        $this->response = $this->ws()->server_get($this->sessionId, $server_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getServerByIp()
    {
        $ip_address     = $this->extractParameter('ip_address');
        $this->response = $this->ws()->server_get_serverid_by_ip($this->sessionId, $ip_address);
        return $this;
    }

    /**
     * @return $this
     */
    public function addCron()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_cron_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteCron()
    {
        $cron_id        = $this->extractParameter('cron_id');
        $this->response = $this->ws()->sites_cron_delete($this->sessionId, $cron_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getCron()
    {
        $cron_id        = $this->extractParameter('cron_id');
        $this->response = $this->ws()->sites_cron_get($this->sessionId, $cron_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateCron()
    {
        $client_id      = $this->extractParameter('client_id');
        $cron_id        = $this->extractParameter('cron_id');
        $this->response = $this->ws()->sites_cron_update($this->sessionId, $client_id, $cron_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addDatabase()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_database_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteDatabase()
    {
        $primary_id     = $this->extractParameter('database_id');
        $this->response = $this->ws()->sites_database_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDatabase()
    {
        $primary_id     = $this->extractParameter('database_id');
        $this->response = $this->ws()->sites_database_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDatabasesByUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_database_get_all_by_user($this->sessionId, $client_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateDatabase()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('database_id');
        $this->response = $this->ws()->sites_database_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addDatabaseUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_database_user_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteDatabaseUser()
    {
        $primary_id     = $this->extractParameter('databaseuser_id');
        $this->response = $this->ws()->sites_database_user_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getDatabaseUser()
    {
        $primary_id     = $this->extractParameter('databaseuser_id');
        $this->response = $this->ws()->sites_database_user_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateDatabaseUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('databaseuser_id');
        $this->response = $this->ws()->sites_database_user_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addFtpUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_ftp_user_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteFtpUser()
    {
        $primary_id     = $this->extractParameter('ftpuser_id');
        $this->response = $this->ws()->sites_ftp_user_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getFtpUser()
    {
        $primary_id     = $this->extractParameter('ftpuser_id');
        $this->response = $this->ws()->sites_ftp_user_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateFtpUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('ftpuser_id');
        $this->response = $this->ws()->sites_ftp_user_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addShellUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_shell_user_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteShellUser()
    {
        $primary_id     = $this->extractParameter('shelluser_id');
        $this->response = $this->ws()->sites_shell_user_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getShellUser()
    {
        $primary_id     = $this->extractParameter('shelluser_id');
        $this->response = $this->ws()->sites_shell_user_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateShellUser()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('shelluser_id');
        $this->response = $this->ws()->sites_shell_user_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addAliasDomain()
    {
        $client_id = $this->extractParameter('client_id');

        $this->response = $this->ws()->sites_web_aliasdomain_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteAliasDomain()
    {
        $primary_id     = $this->extractParameter('aliasdomain_id');
        $this->response = $this->ws()->sites_web_aliasdomain_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getAliasDomain()
    {
        $primary_id     = $this->extractParameter('aliasdomain_id');
        $this->response = $this->ws()->sites_web_aliasdomain_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateAliasDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('aliasdomain_id');
        $this->response = $this->ws()->sites_web_aliasdomain_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addWebDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_web_domain_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteWebDomain()
    {
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->sites_web_domain_delete($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getWebDomain()
    {
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->sites_web_domain_get($this->sessionId, $primary_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function setWebDomainStatus()
    {
        $primary_id     = $this->extractParameter('domain_id');
        $status         = $this->extractParameter('status');
        $this->response = $this->ws()->sites_web_domain_set_status($this->sessionId, $primary_id, $status);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateWebDomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $primary_id     = $this->extractParameter('domain_id');
        $this->response = $this->ws()->sites_web_domain_update($this->sessionId, $client_id, $primary_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function addWebSubdomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $this->response = $this->ws()->sites_web_subdomain_add($this->sessionId, $client_id, $this->params);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteWebsubdomain()
    {
        $subdomain_id   = $this->extractParameter('subdomain_id');
        $this->response = $this->ws()->sites_web_subdomain_delete($this->sessionId, $subdomain_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function getWebSubdomain()
    {
        $subdomain_id   = $this->extractParameter('subdomain_id');
        $this->response = $this->ws()->sites_web_subdomain_get($this->sessionId, $subdomain_id);
        return $this;
    }

    /**
     * @return $this
     */
    public function updateWebSubdomain()
    {
        $client_id      = $this->extractParameter('client_id');
        $subdomain_id   = $this->extractParameter('subdomain_id');
        $this->response = $this->ws()->sites_web_subdomain_update($this->sessionId, $client_id, $subdomain_id, $this->params);
        return $this;
    }

    /**
     *
     */
    public function logout()
    {
        $this->ws()->logout($this->sessionId);
    }
}
