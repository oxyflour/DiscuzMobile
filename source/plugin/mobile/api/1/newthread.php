<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: newthread.php 27783 2012-02-14 07:45:05Z monkey $
 */
//note ���forum >> newthread(����) @ Discuz! X2.0

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'post';
$_GET['action'] = 'newthread';
include_once 'forum.php';

class mobile_api {

	//note ����ģ��ִ��ǰ��Ҫ���еĴ���
	function common() {
	}

	function post_mobile_message($message, $url_forward, $values, $extraparam, $custom) {
		if($values['tid'] && $values['pid']) {
			global $_G;

			$threadstatus = DB::result_first("SELECT status FROM ".DB::table('forum_thread')." WHERE tid='$values[tid]'");
			if(!empty($_POST['allowsound'])) {
				$setstatus = array(1, 0, 0);
			} elseif(!empty($_POST['allowphoto'])) {
				$setstatus = array(0, 1, 1);
			} elseif(!empty($_POST['allowlocal'])) {
				$setstatus = array(0, 1, 0);
			} else {
				$setstatus = array(0, 0, 1);
			}
			foreach($setstatus as $i => $bit) {
				$threadstatus = setstatus(13 - $i, $bit, $threadstatus);
			}
			DB::update('forum_thread', array('status' => $threadstatus), "tid='$values[tid]'");

			$poststatus = DB::result_first("SELECT status FROM ".DB::table('forum_post')." WHERE pid='$values[pid]'");
			$poststatus = setstatus(4, 1, $poststatus);
			if(!empty($_POST['allowlocal'])) {
				$poststatus = setstatus(6, 1, $poststatus);
			}
			if(!empty($_POST['allowsound'])) {
				$poststatus = setstatus(7, 1, $poststatus);
			}
			if(!empty($_POST['mobiletype'])) {
				$mobiletype = base_convert($_POST['mobiletype'], 10, 2);
				if(strlen($mobiletype) < 3) {
					$mobiletype = sprintf('%03d', $mobiletype);
					for($i = 0;$i < 3;$i++) {
						$poststatus = setstatus(10 - $i, $mobiletype{$i}, $poststatus);
					}
				}
			}
			DB::update('forum_post', array('status' => $poststatus), "pid='$values[pid]'");

			list($mapx, $mapy, $location) = explode('|', dhtmlspecialchars($_POST['location']));
			DB::insert('forum_post_location', array(
				'pid' => $values['pid'],
				'tid' => $values['tid'],
				'uid' => $_G['uid'],
				'mapx' => $mapx,
				'mapy' => $mapy,
				'location' => $location,
			));
		}
	}

	//note ����ģ�����ǰ���еĴ���
	function output() {
		global $_G;
		$variable = array(
			'tid' => $GLOBALS['tid'],
			'pid' => $GLOBALS['pid'],
		);
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>