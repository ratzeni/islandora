<?php



class ShowSlideStreamsInFieldSets {

  private $pid =NULL;

  function ShowSlideStreamsInFieldSets($pid) {
    //drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    $this->pid=$pid;
  }
  
  function showJPG() {
    module_load_include('inc', 'fedora_repository', 'plugins/tagging_form');
    module_load_include('php', 'fedora_repository', 'plugins/ShowStreamsInFieldSets');
    global $base_url;

    $tabset = array();

    $tabset['my_tabset'] = array(
      '#type' => 'tabset',
    );
    $tabset['my_tabset']['first_tab'] = array(
      // #type and #title are the minimum requirements.
      '#type' => 'tabpage',
      '#title' => t('View'),
      // This will be the content of the tab.
      '#content' => '<a href="' . $base_url . '/fedora/repository/' . $this->pid . '/FULL_JPG/"><img src="' . $base_url . '/fedora/imageapi/' . $this->pid . '/JPG/JPG.jpg' . '" /></a>'
      .'<p>'.drupal_get_form('fedora_repository_image_tagging_form', $this->pid).'</p>',
    );

    global $user;
    $qs = '';
    if ($user->uid != 0) {
//      $qs = '?uid=' . base64_encode($user->name . ':' . $user->sid);
      $qs = '?uid=' . base64_encode($user->name . ':' . $user->pass);
    }

    $viewer_url = variable_get('fedora_base_url', '') . '/get/' . $this->pid . '/ilives:viewerSdef/getViewer' . $qs;
    $html = '<iframe src="' . $viewer_url .'" frameborder="0" style="width: 100%; height: 800px;">Errors: unable to load viewer</iframe>';

    drupal_add_css(path_to_theme() . '/header-viewer.css', 'theme');

    
    //drupal_add_js(drupal_get_path('module', 'fedora_repository').'/js/iiv/jquery-1.3.2.min.js');
    //drupal_add_js(drupal_get_path('module', 'jquery_ui').'/jquery.ui/ui/jquery.ui.all.js');
    drupal_add_js(drupal_get_path('module', 'fedora_repository').'/js/iiv/jquery-ui-1.7.2.custom.min.js');
    drupal_add_js(drupal_get_path('module', 'fedora_repository').'/js/iiv/OpenLayers-2.7/OpenLayers.js');
    drupal_add_js(drupal_get_path('module', 'fedora_repository').'/js/iiv/iiv.js');
    drupal_add_js('

    function showViewer() {
      var viewer = new iiv.Viewer({
        pid: "'.$this->pid.'",
        cmodel: "islandora:slideCModel",
        dsid: "JP2",
        uid: "'.base64_encode($user->name.':'.$user->pass).'"})
    }

    Drupal.behaviors.myModuleBehavior = function (context) {
    var viewer = new iiv.Viewer({
        pid: "'.$this->pid.'",
        cmodel: "islandora:slideCModel",
        dsid: "JP2",
        uid: "'.base64_encode($user->name.':'.$user->pass).'"})
    };
    ', 'inline');
    
    $tabset['my_tabset']['second_tab'] = array(
//     $collection_fieldset = array (
      '#type' => 'tabpage',
      '#title' => t('Full-size'),
      '#content' => '<div class="iiv"></div>');

    
    $ssifs = new ShowStreamsInFieldSets( $this->pid );
    $tabset['my_tabset']['third_tab'] = array(
      '#type' => 'tabpage',
      '#title' => t('Description'),
      '#content' => $ssifs->showQdc(),
    );
    // Render the tabset.
    return tabs_render($tabset);



    return theme('fieldset', $collection_fieldset);
//      . (user_access('add fedora datastreams') ? drupal_get_form('fedora_ilives_image_tagging_form', $this->pid) : '');
  }
}