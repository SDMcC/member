<?php
    /**
    * -------------------------------.*~ E G N  ~*.------------------------------
    * EGN Software: egMember
    * Copyright (c) 2012-2024 Elwin HG - http://egnsoftware.com (elwin@cvegn.com)
    * ---------------------------------------------------------------------------
    * This software is  furnished  under a  license and may  be used and   copied
    * only  in accordance with the terms of such  license and with  the inclusion
    * of  the above copyright notice.  This software or any other  copies thereof
    * may not be  provided or otherwise made available  to any other person.   No
    * title to and  ownership of the software is hereby transferred.
    * 
    * You  may  not  reverse   engineer,  decompile,  defeat  license  encryption
    * mechanisms, or  disassemble  this  software  product  or software   product
    * license. EGN  may terminate  this license if you  don't comply with any  of
    * the terms and   conditions set forth   in our  End  User  License Agreement
    * (EULA). In  such event, licensee  agrees to return licensor or  destroy all
    * copies of software upon termination  of the license. 
    * Please see the EULA file for the full End User License Agreement.
    * ---------------------------------------------------------------------------
    */
    # cron job
    # run with cron job command for every hours
    require_once ('config.inc.php');
    define('_VALID_MOS_', 1);
    set_time_limit(0);
    error_reporting(E_ALL ^ E_NOTICE);
    ignore_user_abort(true);
    require_once (DOCSPATH . 'includes/eg_cron.php');
    require_once (DOCSPATH . 'includes/general.inc.php');

    $cron = new egCron();
    if ($cron->running_from_commandline()){
        $cron->run_cronfile(basename(__file__));
        exit;
    }

    require_once (DOCSPATH . 'includes/eg_reminder.php');
    require_once (DOCSPATH . 'includes/eg_automail.php');

    $egLanguage = new egLanguage();
    $egLanguage->load_public_language();
    $cron->run();
    exit;
?>