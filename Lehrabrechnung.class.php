<?php

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
        $this->setPluginIconName("images/edit-white.png");
        If($GLOBALS['SessSemName']['class'] == 'sem') $this->seminar_id = $GLOBALS['SessSemName'][1];
        $this->template_factory = new Flexi_TemplateFactory(dirname(__FILE__).'/templates/');
        if($this->seminar_id){
            $this->seminar = Seminar::GetInstance($this->seminar_id);
        }
    }

    function getTabNavigation($course_id)
    {
        if ($this->isVisible()){
            $navigation = new Navigation(_("Lehrabrechnung"));
            $navigation2 = new Navigation(_("Lehrabrechnung"));
            $navigation2->setUrl(PluginEngine::getUrl($this, array('action' => 'main')));
            $navigation->addSubnavigation('main', $navigation2);
            $navigation->setImage(Assets::image_path('/images/icons/16/white/edit.png'));
            $navigation->setActiveImage(Assets::image_path('/images/icons/16/black/edit.png'));
            return array(get_class($this) => $navigation);
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

    function display_action($action) {
        ob_start();
        $this->$action();
        PageLayout::setTitle($GLOBALS['SessSemName']["header_line"]. " - " . _("Lehrabrechnung"));
        $layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox.php');
        $layout->content_for_layout = ob_get_clean();
        echo $layout->render();
    }

    function actionShow(){
        if (!$this->isVisible()) throw new AccessDeniedException();
        Navigation::activateItem('/course/' . get_class($this) . '/main');
        $msg = array();
        if(Request::submitted('save')){
            $sws_seminar = round(str_replace(',','.', trim(Request::get('sws_seminar'))),2);
            if($sws_seminar != MluLVVO::GetSeminarSWS($this->seminar_id)){
                if(MluLVVO::SetSeminarSWS($this->seminar_id, $sws_seminar)){
                    $msg[] = array('msg', _("SWS der Lehrveranstaltung wurde geändert."));
                }
            }

            foreach(Request::getArray('sws_user') as $user_id => $sws_user){
                $sws_user = round(str_replace(',','.', trim($sws_user)),2);
                $lvvo_entry = new MluLVVO(array($this->seminar_id, $user_id));
                $lvvo_entry->setValue('sws_user', $sws_user);
                $lvvo_entry->setValue('last_changed_user_id', $GLOBALS['user']->id);
                if($lvvo_entry->store()){
                    $msg[] = array('msg', sprintf(_("Lehrveranstaltungsanteile von %s wurden geändert."), htmlReady(get_fullname($user_id, 'no_title'))));
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
