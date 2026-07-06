<?php

use EvolutionCMS\Models\SiteTmplvarAccess;
use EvolutionCMS\Models\SiteTmplvarTemplate;


if (!function_exists('evalModule')) {
    /**
     * evalModule
     *
     * @param string $moduleCode
     * @param array $params
     * @return string
     */
    function evalModule($moduleCode, $params)
    {
        $modx = evo();
        $modx->event->params = &$params; // store params inside event object
        if (is_array($params)) {
            extract($params, EXTR_SKIP);
        }
        ob_start();
        $mod = eval($moduleCode);
        $msg = ob_get_contents();
        ob_end_clean();
        if (isset($php_errormsg)) {
            $error_info = error_get_last();
            switch ($error_info['type']) {
                case E_NOTICE :
                    $error_level = 1;
                case E_USER_NOTICE :
                    break;
                case E_DEPRECATED :
                case E_USER_DEPRECATED :
                    $error_level = 2;
                    break;
                default:
                    $error_level = 99;
            }
            if ($modx->getConfig('error_reporting') >= 99 || 2 < $error_level) {
                $modx->messageQuit(
                    'PHP Parse Error'
                    , ''
                    , true
                    , $error_info['type']
                    , $error_info['file']
                    , $_SESSION['itemname'] . ' - Module'
                    , $error_info['message']
                    , $error_info['line']
                    , $msg
                );
                $modx->event->alert(
                    'An error occurred while loading. Please see the event log for more information<p>' . $msg . '</p>'
                );
            }
        }
        unset($modx->event->params);

        return $mod . $msg;
    }
}

if (!function_exists('allChildren')) {
    /**
     * @param int $currDocID
     * @return array
     */
    function allChildren($currDocID)
    {
        $children = [];
        $found = collect();

        $docs = EvolutionCMS\Models\SiteContent::withTrashed()
            ->where('parent', '=', $currDocID)
            ->pluck('id')
            ->toArray();

        foreach ($docs as $id) {
            $children[] = $id;
            $found->push(allChildren($id));
        }

        return array_merge($children, $found->collapse()->toArray());
    }
}

if (!function_exists('jsAlert')) {
    /**
     * show javascript alert
     *
     * @param string $msg
     */
    function jsAlert($msg)
    {
        if ((int)get_by_key($_POST, 'ajax', 0) !== 1) {
            echo '<script>window.setTimeout("alert(\'' . addslashes($msg) . '\')",10);history.go(-1)</script>';
        } else {
            echo $msg . "\n";
        }
    }
}

if (!function_exists('login')) {
    /**
     * @param string $username
     * @param string $givenPassword
     * @param string $dbasePassword
     * @return bool
     */
    function login($username, $givenPassword, $dbasePassword)
    {
        $modx = evo();

        return $modx->getPasswordHash()->CheckPassword($givenPassword, $dbasePassword);
    }
}

if (!function_exists('loginV1')) {
    /**
     * @param int $internalKey
     * @param string $givenPassword
     * @param string $dbasePassword
     * @param string $username
     * @return bool
     */
    function loginV1($internalKey, $givenPassword, $dbasePassword, $username)
    {
        $modx = evo();

        $user_algo = $modx->getManagerApi()->getV1UserHashAlgorithm($internalKey);

        if (!isset($modx->config['pwd_hash_algo']) || empty($modx->config['pwd_hash_algo'])) {
            $modx->setConfig('pwd_hash_algo', 'UNCRYPT');
        }

        if ($user_algo !== $modx->getConfig('pwd_hash_algo')) {
            $bk_pwd_hash_algo = $modx->getConfig('pwd_hash_algo');
            $modx->setConfig('pwd_hash_algo', $user_algo);
        }

        if ($dbasePassword != $modx->getManagerApi()->genV1Hash($givenPassword, $internalKey)) {
            return false;
        }

        updateNewHash($username, $givenPassword);

        return true;
    }
}

if (!function_exists('loginMD5')) {
    /**
     * @param int $internalKey
     * @param string $givenPassword
     * @param string $dbasePassword
     * @param string $username
     * @return bool
     */
    function loginMD5($internalKey, $givenPassword, $dbasePassword, $username)
    {
        $modx = evo();

        if ($dbasePassword != md5($givenPassword)) {
            return false;
        }
        updateNewHash($username, $givenPassword);

        return true;
    }
}

if (!function_exists('updateNewHash')) {
    /**
     * @param string $username
     * @param string $password
     */
    function updateNewHash($username, $password)
    {
        $modx = evo();

        $field = [];
        $field['password'] = $modx->getPasswordHash()->HashPassword($password);
        \EvolutionCMS\Models\User::where('username', $username)->update($field);

    }
}

if (!function_exists('saveUserGroupAccessPermissons')) {
    /**
     * Saves module user group access permissions
     *
     * @param int $moduleId The module ID to save permissions for
     * @param bool $enableUpdate Whether to update permissions (use_udperms config)
     * @return void
     */
    function saveUserGroupAccessPermissons($moduleId, $enableUpdate = true)
    {
        $usrgroups = get_by_key($_POST, 'usrgroups', []);

        // check for permission update access
        if ($enableUpdate == 1) {
            // delete old permissions on the module
            \EvolutionCMS\Models\SiteModuleAccess::where('module', $moduleId)->delete();

            if (is_array($usrgroups)) {
                foreach ($usrgroups as $value) {
                    \EvolutionCMS\Models\SiteModuleAccess::create([
                        'module' => (int)$moduleId,
                        'usergroup' => stripslashes($value),
                    ]);
                }
            }
        }
    }
}

if (!function_exists('saveEventListeners')) {
# Save Plugin Event Listeners
    /**
     * @param int $id
     * @param array $sysevents
     * @param string $mode
     * @return void
     */
    function saveEventListeners($id, $sysevents, $mode)
    {
        // save selected system events
        $formEventList = [];
        foreach ($sysevents as $evtId) {
            if (!preg_match('@^[1-9][0-9]*$@', $evtId)) {
                $evtId = getEventIdByName($evtId);
            }
            if ($mode == '101') {
                $prevPriority = \EvolutionCMS\Models\SitePluginEvent::query()->where('evtid', $evtId)->max('priority');
            } else {
                $prevPriority = \EvolutionCMS\Models\SitePluginEvent::query()->where('evtid', $evtId)
                    ->where('pluginid', $id)->max('priority');
            }
            if ($mode == '101') {
                $priority = isset($prevPriority) ? $prevPriority + 1 : 1;
            } else {
                $priority = isset($prevPriority) ? $prevPriority : 1;
            }
            $priority = (int)$priority;
            $formEventList[] = ['pluginid' => $id, 'evtid' => $evtId, 'priority' => $priority];
        }

        $evtids = [];
        foreach ($formEventList as $eventInfo) {
            \EvolutionCMS\Models\SitePluginEvent::query()->updateOrCreate(['pluginid' => $eventInfo['pluginid'], 'evtid' => $eventInfo['evtid']], ['priority' => $eventInfo['priority']]);
            $evtids[] = $eventInfo['evtid'];
        }
        $pluginEvents = \EvolutionCMS\Models\SitePluginEvent::query()->where('pluginid', $id)->get();

        $del = [];
        foreach ($pluginEvents->toArray() as $row) {
            if (!in_array($row['evtid'], $evtids)) {
                $del[] = $row['evtid'];
            }
        }

        if (empty($del)) {
            return;
        }
        \EvolutionCMS\Models\SitePluginEvent::query()->where('pluginid', $id)->whereIn('evtid', $del)->delete();

    }
}

if (!function_exists('getEventIdByName')) {
    /**
     * @param string $name
     * @return string|int|null
     */
    function getEventIdByName($name)
    {
        static $eventIds = [];

        if (isset($eventIds[$name])) {
            return $eventIds[$name];
        }
        $eventIds = \EvolutionCMS\Models\SystemEventname::query()->pluck('id', 'name')->toArray();

        return $eventIds[$name] ?? null;
    }
}

if (!function_exists('saveTemplateAccess')) {
    /**
     * @param int $id
     */
    function saveTemplateAccess($id)
    {
        if ((int)($_POST['tvsDirty'] ?? 0) == 1) {
            $newAssignedTvs = isset($_POST['assignedTv']) ? $_POST['assignedTv'] : [];

            // Preserve rankings of already assigned TVs
            $templates = SiteTmplvarTemplate::query()->where('templateid', $id)->get();
            $ranksArr = [];
            $highest = 0;
            foreach ($templates->toArray() as $row) {
                $ranksArr[$row['tmplvarid']] = $row['rank'];
                $highest = $highest < $row['rank'] ? $row['rank'] : $highest;
            };
            SiteTmplvarTemplate::query()->where('templateid', $id)->delete();

            if (empty($newAssignedTvs)) {
                return;
            }
            foreach ($newAssignedTvs as $tvid) {
                if (!$id || !$tvid) {
                    continue;
                }    // Dont link zeros
                SiteTmplvarTemplate::create([
                    'templateid' => $id,
                    'tmplvarid' => $tvid,
                    'rank' => isset($ranksArr[$tvid]) ? $ranksArr[$tvid] : $highest += 1 // append TVs to rank
                ]);
            }
        }
    }
}

if (!function_exists('saveTemplateVarAccess')) {
    /**
     * @param int $id
     * @return void
     */
    function saveTemplateVarAccess($id)
    {
        $templates = isset($_POST['template']) ? $_POST['template'] : []; // get muli-templates based on S.BRENNAN mod

        $siteTmlvarTemplates = EvolutionCMS\Models\SiteTmplvarTemplate::where('tmplvarid', '=', $id)->get();

        $getRankArray = $siteTmlvarTemplates->pluck('rank', 'templateid')->toArray();

        EvolutionCMS\Models\SiteTmplvarTemplate::where('tmplvarid', '=', $id)->delete();
        if (!$templates) {
            return;
        }
        foreach ($templates as $i => $iValue) {
            $field = [
                'tmplvarid' => $id,
                'templateid' => $templates[$i],
                'rank' => get_by_key($getRankArray, $iValue, 0)
            ];
            EvolutionCMS\Models\SiteTmplvarTemplate::create($field);
        }
    }
}

if (!function_exists('saveVarRoles')) {
    /**
     * @param int $id
     * @return void
     */
    function saveVarRoles($id)
    {
        $roles = isset($_POST['role']) ? $_POST['role'] : [];

        $exists = EvolutionCMS\Models\UserRoleVar::where('tmplvarid', '=', $id)->get();

        $getRankArray = $exists->pluck('rank', 'roleid')->toArray();

        EvolutionCMS\Models\UserRoleVar::where('tmplvarid', '=', $id)->delete();
        if (!$roles) {
            return;
        }
        foreach ($roles as $i => $iValue) {
            $field = [
                'tmplvarid' => $id,
                'roleid' => $roles[$i],
                'rank' => get_by_key($getRankArray, $iValue, 0)
            ];
            EvolutionCMS\Models\UserRoleVar::create($field);
        }
    }
}

if (!function_exists('saveDocumentAccessPermissons')) {
    /**
     * @param int $id
     * @return void
     */
    function saveDocumentAccessPermissons($id)
    {
        $modx = evo();

        $docgroups = isset($_POST['docgroups']) ? $_POST['docgroups'] : [];

        // check for permission update access
        if ($modx->getConfig('use_udperms') != 1) {
            return;
        }

        // delete old permissions on the tv
        EvolutionCMS\Models\SiteTmplvarAccess::where('tmplvarid', '=', $id)->delete();
        if (is_array($docgroups)) {
            foreach ($docgroups as $value) {
                $field = ['tmplvarid' => $id, 'documentgroup' => stripslashes($value)];
                EvolutionCMS\Models\SiteTmplvarAccess::create($field);
            }
        }
    }
}

if (!function_exists('sendMailMessageForUser')) {
    /**
     * Send an email to the user
     *
     * @param string $email User email address
     * @param string $uid Username/user ID
     * @param string $pwd Password to send
     * @param string $ufn User full name
     * @param string $message Email template message
     * @param string $url Site URL for links
     * @param array $config Optional configuration override (from, subject, lang)
     * @return bool True if sent successfully
     */
    function sendMailMessageForUser($email, $uid, $pwd, $ufn, $message, $url, $config = [])
    {
        $modx = evo();
        
        // Get configuration with defaults
        $emailSender = $config['from'] ?? '';
        $emailSubject = $config['subject'] ?? 'User Account';
        $lang = $config['lang'] ?? [];
        
        // Format message with credentials
        $message = sprintf($message, $uid, $pwd);
        
        // Get user attributes
        $last_name = '';
        $first_name = '';
        $middle_name = '';
        $user = \EvolutionCMS\Models\UserAttribute::query()->where('email', $email)->first();
        if (!is_null($user)) {
            $last_name = $user->last_name;
            $first_name = $user->first_name;
            $middle_name = $user->middle_name;
        }
        
        // Replace placeholder variables
        $message = str_replace(
            [
                '[+uid+]', '[+pwd+]', '[+ufn+]', 
                '[+sname+]', '[+saddr+]', '[+semail+]', '[+surl+]',
                '[+u_first_name+]', '[+u_last_name+]', '[+u_middle_name+]'
            ],
            [
                $uid, $pwd, $ufn,
                $modx->getPhpCompat()->entities($modx->getConfig('site_name')),
                $emailSender,
                $emailSender,
                $url,
                $first_name, $last_name, $middle_name
            ],
            $message
        );

        // Prepare email parameters
        $param = [];
        $param['from'] = $modx->getConfig('site_name') . ' <' . $emailSender . '>';
        $param['subject'] = $emailSubject;
        $param['body'] = $message;
        $param['to'] = $email;
        $param['type'] = 'text';
        
        // Send email
        if ($modx->sendmail($param)) {
            return true;
        }
        
        // Email failed - save form and display error
        $modx->getManagerApi()->saveFormValues();
        $errorMsg = isset($lang['error_sending_email']) 
            ? $email . ' - ' . $lang['error_sending_email']
            : $email . ' - Error sending email';
        $modx->messageQuit($errorMsg);
        return false;
    }
}


if (!function_exists('webAlertAndQuit')) {
    /**
     * Web alert -  sends an alert to web browser
     *
     * @param string $msg
     * @param int $action
     * @param int|bool $id
     */
    function webAlertAndQuit($msg, $action, $id = false)
    {
        $modx = EvolutionCMS();
        $mode = $_POST['mode'];
        $modx->getManagerApi()->saveFormValues($mode);
        $url = 'index.php?a=' . $action;
        if ($id != false) {
            $url .= '&id=' . $id;
        }
        $modx->webAlertAndQuit($msg, $url);
    }
}