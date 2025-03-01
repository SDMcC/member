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
    if (defined('DOCSPATH')) require_once (DOCSPATH . 'config.inc.php');
    else  require_once ('../config.inc.php');
    define('_VALID_MOS_', 1);
    define('BOPATH', dirname(__FILE__) . '/');
    require_once (DOCSPATH . 'includes/general.inc.php');
    require_once (DOCSPATH . 'includes/eg_ssl.php');
    require_once (DOCSPATH . 'includes/eg_admin.php');

    $egAdmin = new egAdmin();
    $egAdmin->initiate_admin();
    $egTpl = new egTemplater();

    require_once (DOCSPATH . 'includes/eg_reminder.php');
    require_once (DOCSPATH . 'includes/eg_automail.php');
    require_once (DOCSPATH . 'includes/eg_export.php');

    if ($config['log_admin'] == '1') {
        egStats::write_admin_stats($admin_data['userid'],2);
    }

    $userlevels = get_membership_levels();
    $feature = egFeatures::get_config();

    // Admin menus
    // ----------- Dashboard -----------   
    $admin_main_menus = array();    
    $admin_main_menus[] = array( 'fa' =>'fa fa-house-user', 'title' => _admin_home, 'url' => 'index.php'); //0
    $admin_sub_menus[] = $_sub_menus;


    // ----------- Members -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-users', 'title' => _members, 'url' => 'members.php', 'backurl' => 'members.php?do=list'); //1

    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "members.php", 'title' => _summary);

    $_sub_menus[] = array( 'url' => "members.php?do=list", 'title' => _member_list);
    $_sub_menus[] = array( 'url' => "members.php?do=balance", 'title' => _member_balances);
    $_sub_menus[] = array( 'url' => "members.php?do=ranks", 'title' => _member_ranks);
    $_sub_menus[] = array( 'url' => "members.php?do=rating", 'title' => _rating_score);
    $_sub_menus[] = array( 'url' => "members.php?do=top", 'title' => _top_networker);

    if ($config['document_verification']) {
        $_sub_menus[] = array( 'url' => "members.php?do=verification", 'title' => _document_verification);
    }


    $_sub_menus[] = array( 'url' => "members.php?do=add", 'title' => _member_add);
    $_sub_menus[] = array( 'url' => "members.php?do=export", 'title' => _member_export);
    $admin_sub_menus[] = $_sub_menus;


    if(egFeatures::feature_enabled('product')) { 
        // ----------- Products  -----------
        $admin_main_menus[] = array( 'fa' =>'fa fa-store', 'title' => _product, 'url' => 'products.php', 'backurl' => 'products.php?do=articles'); //3
        $_sub_menus = array();
        $_sub_menus[] = array( 'url' => "products.php", 'title' => _summary);
        $_sub_menus[] = array( 'url' => "products.php?do=articles", 'title' => _str_article);
        $_sub_menus[] = array( 'url' => "products.php?do=downloads", 'title' => _downloads);
        $admin_sub_menus[] = $_sub_menus;
    }

    if(egFeatures::feature_enabled('epin')) { 
        // ----------- Epin -----------
        $admin_main_menus[] = array( 'fa' =>'fa fa-chalkboard-teacher', 'title' => _epin, 'url' => 'epin.php'); //7
        $_sub_menus = array();
        $_sub_menus[] = array( 'url' => "epin.php", 'title' => _summary);
        $_sub_menus[] = array( 'url' => "epin.php?do=free", 'title' => _epin_free_list);
        $_sub_menus[] = array( 'url' => "epin.php?do=ordered", 'title' => _epin_ordered_list);
        $_sub_menus[] = array( 'url' => "epin.php?do=used", 'title' => _epin_used_list);
        $_sub_menus[] = array( 'url' => "epin.php?do=shops", 'title' => _epin_shop_list);
        $_sub_menus[] = array( 'url' => "epin.php?do=packages", 'title' => _epin_packages);
        $_sub_menus[] = array( 'url' => "epin.php?do=create", 'title' => _epin_create);
        $admin_sub_menus[] = $_sub_menus;
    }



    // ----------- Rewards -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-gift', 'title' => _gift, 'url' => 'rewards.php'); //10
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "rewards.php", 'title' => _summary);
    $_sub_menus[] = array( 'url' => "rewards.php?do=rank", 'title' => _reward_rank);
    $_sub_menus[] = array( 'url' => "rewards.php?do=pool", 'title' => _reward_pool);
    $admin_sub_menus[] = $_sub_menus;



    // ----------- Payment -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-money-bill-wave', 'title' => _payments, 'url' => 'payment.php'); //9
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "payment.php", 'title' => _summary);
    $_sub_menus[] = array( 'url' => "payment.php?do=addfund", 'title' => _addfund_payment);
    $_sub_menus[] = array( 'url' => "payment.php?do=membership", 'title' => _membership_payment); 

    if(egFeatures::feature_enabled('epin')) {
        $_sub_menus[] = array( 'url' => "payment.php?do=epin", 'title' => _epin_payment);
    }

    $admin_sub_menus[] = $_sub_menus;



    // ----------- Withdrawal -----------
    $admin_main_menus[] = array( 'fa' =>'fas fa-money-check', 'title' => _withdrawal, 'url' => 'withdrawal.php'); //9
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "withdrawal.php", 'title' => _summary);
    $_sub_menus[] = array( 'url' => "withdrawal.php?do=list", 'title' => _withdrawal_request);
    $_sub_menus[] = array( 'url' => "withdrawal.php?do=review", 'title' => _withdrawal_verification);
    $_sub_menus[] = array( 'url' => "withdrawal.php?do=accounts", 'title' => _withdrawal_approved);

    $admin_sub_menus[] = $_sub_menus;



    // ----------- Logs -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-chart-bar', 'title' => _log, 'url' => 'logs.php'); //11
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "logs.php?do=earning", 'title' => _log_earning);
    $_sub_menus[] = array( 'url' => "logs.php?do=point", 'title' => _log_points);
    $_sub_menus[] = array( 'url' => "logs.php?do=point_redeem", 'title' => _log_points_redeem);
    $_sub_menus[] = array( 'url' => "logs.php?do=transfer", 'title' => _log_transfer);
    $_sub_menus[] = array( 'url' => "logs.php?do=access", 'title' => _log_access);
    $_sub_menus[] = array( 'url' => "logs.php?do=stats", 'title' => _log_stats);
    $_sub_menus[] = array( 'url' => "logs.php?do=error", 'title' => _log_error);
    $_sub_menus[] = array( 'url' => "logs.php?do=cron", 'title' => _cron_task);
    $_sub_menus[] = array( 'url' => "logs.php?do=onliners", 'title' => _log_onliners);



    if($config['log_account_activity']) {
        $_sub_menus[] = array( 'url' => "logs.php?do=activity", 'title' => _log_activity);
    }

    $admin_sub_menus[] = $_sub_menus;


    // ----------- Contact -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-mail-bulk', 'title' => _contacts, 'url' => 'contact.php'); //12
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "contact.php?do=whatsapp", 'title' => _whatsapp_send);
    $_sub_menus[] = array( 'url' => "contact.php?do=email", 'title' => _email_send);
    $_sub_menus[] = array( 'url' => "contact.php?do=automail", 'title' => _email_automail);

    $admin_sub_menus[] = $_sub_menus;


    // ----------- Tickets -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-envelope-open-text', 'title' => _ticket, 'url' => 'ticket.php'); //13
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "ticket.php", 'title' => _open_messages);
    $_sub_menus[] = array( 'url' => "ticket.php?do=answered", 'title' => _answered_messages);
    $_sub_menus[] = array( 'url' => "ticket.php?do=closed", 'title' => _closed_messages);
    $_sub_menus[] = array( 'url' => "ticket.php?do=create", 'title' => _create_message);
    $_sub_menus[] = array( 'url' => "ticket.php?do=config", 'title' => _feature_setting);
    $admin_sub_menus[] = $_sub_menus;


    // ----------- Management -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-tasks', 'title' => _management, 'url' => 'management.php'); //14
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "management.php?do=news", 'title' => _news);
    $_sub_menus[] = array( 'url' => "management.php?do=testimonial", 'title' => _testimonial);


    $_sub_menus['content'] = array( 'url' => "#", 'title' => _str_content);
    $_sub_menus['content']['subs'][] = array( 'url' => "management.php?do=content_member", 'title' => _str_content_member);
    $_sub_menus['content']['subs'][] = array( 'url' => "management.php?do=content_frontpage", 'title' => _str_content_frontpage);



    $_sub_menus['menus'] = array( 'url' => "#", 'title' => _str_menus);
    $_sub_menus['menus']['subs'][] = array( 'url' => "management.php?do=menus_member", 'title' => _str_member_menus);
    $_sub_menus['menus']['subs'][] = array( 'url' => "management.php?do=menus_frontpage", 'title' => _str_frontpage_menus);



    $_sub_menus[] = array( 'url' => "management.php?do=faqs", 'title' => _faqs);
    $_sub_menus[] = array( 'url' => "management.php?do=landing_pages", 'title' => _landing_pages);
    $_sub_menus[] = array( 'url' => "management.php?do=banners", 'title' => _ref_banners);
    $_sub_menus[] = array( 'url' => "management.php?do=report", 'title' => _str_report);
    $_sub_menus[] = array( 'url' => "management.php?do=backup", 'title' => _str_backup);
    $admin_sub_menus[] = $_sub_menus;


    // ----------- Configuration -----------
    $admin_main_menus[] = array('fa' =>'fa fa-cogs', 'icon' => 'admin-menu-configuration', 'title' => _config, 'url' => 'configuration.php'); //15
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "configuration.php?do=general", 'title' => _config_general);
    $_sub_menus[] = array( 'url' => "configuration.php?do=membership", 'title' => _config_membership);
    $_sub_menus[] = array( 'url' => "configuration.php?do=packages", 'title' => _config_packages);
    $_sub_menus[] = array( 'url' => "configuration.php?do=fields", 'title' => _config_fields);
    $_sub_menus[] = array( 'url' => "configuration.php?do=language", 'title' => _config_language);
    $_sub_menus[] = array( 'url' => "configuration.php?do=mailing", 'title' => _config_mail);
    $_sub_menus[] = array( 'url' => "configuration.php?do=text", 'title' => _sms_notification);
    $_sub_menus[] = array( 'url' => "configuration.php?do=payment", 'title' => _config_payment);
    $_sub_menus[] = array( 'url' => "configuration.php?do=withdrawal", 'title' => _config_withdrawal);
    $_sub_menus[] = array( 'url' => "configuration.php?do=automation", 'title' => _str_automation);
    $admin_sub_menus[] = $_sub_menus;


    // ----------- Plugins -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-tools', 'title' => _plugin, 'url' => 'plugin.php'); //16
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "plugin.php?do=frontpage", 'title' => _plugin_frontpage);
    $_sub_menus[] = array( 'url' => "plugin.php?do=photos", 'title' => _plugin_photo);
    $_sub_menus[] = array( 'url' => "plugin.php?do=pdf", 'title' => _str_viralpdf);
    $_sub_menus[] = array( 'url' => "plugin.php?do=im", 'title' => _plugin_im);
    $_sub_menus[] = array( 'url' => "plugin.php?do=captcha", 'title' => _plugin_captcha);
    $_sub_menus[] = array( 'url' => "plugin.php?do=ban", 'title' => _plugin_ban);
    $_sub_menus[] = array( 'url' => "plugin.php?do=bruteforce", 'title' => _plugin_bruteforce);
    $_sub_menus[] = array( 'url' => "plugin.php?do=mailqueue", 'title' => _plugin_mailqueue);
    $_sub_menus[] = array( 'url' => "plugin.php?do=whatsapp", 'title' => _whatsapp_text_msg);
    $_sub_menus[] = array( 'url' => "plugin.php?do=tawk", 'title' => 'Tawk.to Live chat');

    $admin_sub_menus[] = $_sub_menus;





    // ----------- Admin -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-user-shield', 'title' => _admin, 'url' => 'admin.php'); //17
    $_sub_menus = array();
    $_sub_menus[] = array( 'url' => "admin.php?do=profile", 'title' => _admin_profile);
    $_sub_menus[] = array( 'url' => "admin.php?do=calendar", 'title' => _calendar);
    $_sub_menus[] = array( 'url' => "admin.php?do=bookmarks", 'title' => _admin_bookmarks);
    $_sub_menus[] = array( 'url' => "admin.php?do=list", 'title' => _admin_list);
    $_sub_menus[] = array( 'url' => "admin.php?do=add", 'title' => _admin_add);
    $_sub_menus[] = array( 'url' => "admin.php?do=logs", 'title' => _admin_log);
    $_sub_menus[] = array( 'url' => "admin.php?do=access", 'title' => _admin_access);
    $_sub_menus[] = array( 'url' => "admin.php?do=egnsupport", 'title' => _egn_support);
    $admin_sub_menus[] = $_sub_menus;


    // ----------- Logout -----------
    $admin_main_menus[] = array( 'fa' =>'fa fa-sign-out-alt', 'title' => _str_logout, 'url' => 'admin.php?do=logout'); //18

    $adminMenus = new egAdminMenus($admin_main_menus, $admin_sub_menus);

    //$PARENT_MENU = egAdmin::get_parent_menu_id();


?>