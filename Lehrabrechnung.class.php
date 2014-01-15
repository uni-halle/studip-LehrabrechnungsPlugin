<?php

require_once "vendor/flexi/flexi.php";
require_once "lib/classes/Seminar.class.php";
require_once "MluLVVO.class.php";

class Lehrabrechnung extends AbstractStudIPStandardPlugin {

	public $template_factory;
	public $seminar;
	public $seminar_id;
	public $allowed_sem_class = 1;

	/**
	 *
	 */
	function __construct(){
		parent::__construct();
		$this->setPluginiconname('images/icon_zensus_neu.gif');
		If($GLOBALS['SessSemName']['class'] == 'sem') $this->seminar_id = $GLOBALS['SessSemName'][1];
		$this->template_factory = new Flexi_TemplateFactory(dirname(__FILE__).'/templates/');
		if($this->seminar_id){
			$this->seminar = Seminar::GetInstance($this->seminar_id);
		}
		if ($this->isVisible()){
			$navigation = new PluginNavigation();
			$navigation->setDisplayname(_("Lehrabrechnung"));
			$navigation->addLinkParam('action', 'main');
			$this->setNavigation($navigation);
		}
	}

	function isVisible(){
		if(is_object($this->seminar) && $GLOBALS['perm']->have_studip_perm('tutor', $this->seminar_id)){
			foreach($GLOBALS['SEM_TYPE'] as $type_key => $type_value){
				if($type_value['class'] == $this->allowed_sem_class && $type_key == $this->seminar->status){
					return true;
				}
			}
		}
		return false;
	}

	function actionShow(){
		if (!$this->isVisible()) throw new AccessDeniedException();
		$msg = array();
		if(isset($_REQUEST['save_x'])){
			$sws_seminar = round(str_replace(',','.', trim($_REQUEST['sws_seminar'])),2);
			if($sws_seminar != MluLVVO::GetSeminarSWS($this->seminar_id)){
					if(MluLVVO::SetSeminarSWS($this->seminar_id, $sws_seminar)){
						$msg[] = array('msg', _("SWS der Lehrveranstaltung wurde geändert."));
					}
			}
			if(is_array($_REQUEST['sws_user'])){
				foreach($_REQUEST['sws_user'] as $user_id => $sws_user){
					$sws_user = round(str_replace(',','.', trim($sws_user)),2);
					$lvvo_entry = new MluLVVO(array($this->seminar_id, $user_id));
					$lvvo_entry->setValue('sws_user', $sws_user);
					$lvvo_entry->setValue('last_changed_user_id', $GLOBALS['user']->id);
					if($lvvo_entry->store()){
						$msg[] = array('msg', sprintf(_("Lehrveranstaltungsanteile von %s wurden geändert."), htmlReady(get_fullname($user_id, 'no_title'))));
					}
				}
			}
		}
		$lvvo_data = MluLVVO::GetSeminarLVVO($this->seminar_id);
		$sws_seminar = MluLVVO::GetSeminarSWS($this->seminar_id);
		$template = $this->template_factory->open('main');
		$template->set_attribute('msg', $msg);
        $template->set_attribute('plugin', $this);
		$template->set_attribute('lvvo_data', $lvvo_data);
		$template->set_attribute('sws_seminar', $sws_seminar);
        echo $template->render();
	}
}
?>
