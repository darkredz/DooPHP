<?php

// anonymous user can only access Blog index page.
$acl['anonymous']['allow'] = array(
							'BlogController'=>array('index')
						);

$acl['vip']['allow'] = 
$acl['member']['allow'] = array(
							'SnsController'=>'*', 
							'BlogController'=>'*', 
						);
						
$acl['member']['deny'] = array(
							'SnsController'=>array('banUser', 'showVipHome'), 
							'BlogController' =>array('deleteComment', 'writePost')
						);

$acl['vip']['deny'] = array(
							'SnsController'=>array('banUser'), 
							'BlogController' =>array('deleteComment', 'writePost')
						);

$acl['admin']['allow'] = '*';
$acl['admin']['deny'] = array(
							'SnsController'=>array('showVipHome')
						);
						
						
$acl['member']['failRoute'] = array(
								'_default'=>'/error/member',	//if not found this will be used
								'SnsController/banUser'=>'/error/member/sns/notAdmin', 
								'SnsController/showVipHome'=>'/error/member/sns/notVip', 
								'BlogController'=>'/error/member/blog/notAdmin' 
							);

$acl['vip']['failRoute'] = array(
								'_default'=>'/error/vip',	//if not found this will be used
								'SnsController/banUser'=>'/error/member/sns/notAdmin', 
								'BlogController'=>'/error/member/blog/notAdmin' 
							);					

$acl['admin']['failRoute'] = array(
								'SnsController/showVipHome'=>'/error/admin/sns/vipOnly' 
							);

$acl['anonymous']['failRoute'] = array(
								'_default'=>'/error/loginfirst',
							);								
?>