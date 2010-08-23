<?php

/**
 * Implementation of hook_theme().
 */
function ginkgo_theme() {
  $items = array();

  // Use simple form.
  $items['comment_form'] =
  $items['user_pass'] =
  $items['user_login'] =
  $items['user_register'] = array(
    'arguments' => array('form' => array()),
    'path' => drupal_get_path('theme', 'rubik') .'/templates',
    'template' => 'form-simple',
    'preprocess functions' => array(
      'rubik_preprocess_form_buttons',
      'rubik_preprocess_form_legacy'
    ),
  );
  return $items;
}

/**
 * Add an href-based class to links for themers to implement icons.
 */
function ginkgo_icon_links(&$links) {
  if (!empty($links)) {
    foreach ($links as $k => $v) {
      if (empty($v['attributes'])) {
        $v['attributes'] = array('class' => '');
      }
      else if (empty($v['attributes']['class'])) {
        $v['attributes']['class'] = '';
      }
      $v['attributes']['class'] .= ' icon-'. _ginkgo_icon_class($v['href']);

      // Detect and replace counter occurrences with markup.
      $start = strpos($v['title'], '(');
      $end = strpos($v['title'], ')');
      if ($start !== FALSE && $end !== FALSE && $start < $end) {
        $v['title'] = strtr($v['title'], array('(' => "<span class='count'>", ')' => "</span>"));
      }

      $v['title'] = filter_xss_admin("<span class='icon'></span><span class='label'>". $v['title'] ."</span>");
      $v['html'] = TRUE;
      $links[$k] = $v;
    }
  }
}

/**
 * Preprocess overrides ===============================================
 */

/**
 * Preprocessor for theme_page().
 */
function ginkgo_preprocess_page(&$vars) {
  // Switch layout for 404/403 pages.
  $headers = drupal_get_headers();
  if ((strpos($headers, 'HTTP/1.1 403 Forbidden') !== FALSE) || strpos($headers, 'HTTP/1.1 404 Not Found') !== FALSE) {
    $vars['template_files'][] = 'layout-wide';
  }

  // Add body class for layout.
  $vars['attr']['class'] .= !empty($vars['template_files']) ? ' '. end($vars['template_files']) : '';

  // Don't show the navigation in the admin section.
  // Otherwise add icon markup to main menu.
  if (arg(0) === 'admin') {
    $vars['primary_links'] = '';
  }
  else {
    ginkgo_icon_links($vars['primary_links']);
  }

  // If tabs are active, the title is likely shown in them. Don't show twice.
  $vars['title_attr'] = array('class' => 'page-title');
  $vars['title_attr']['class'] .= (!empty($vars['tabs']) || menu_get_object()) ? ' page-title-hidden' : '';

  // Show mission text on login page for anonymous users.
  global $user;
  $vars['mission'] = (!$user->uid && arg(0) == 'user') ? filter_xss_admin(variable_get('site_mission', '')) : '';

  // Fallback logo.
  $vars['logo'] = !empty($vars['logo']) ? $vars['logo'] : l(check_plain(variable_get('site_name', 'Drupal')), '<front>', array('attributes' => array('class' => 'logo')));

  // Footer links
  $vars['footer_links'] = isset($vars['footer_links']) ? $vars['footer_links'] : array();
  $item = menu_get_item('admin');
  if ($item && $item['access']) {
    $vars['footer_links']['admin'] = $item;
  }

  // IE7 CSS
  // @TODO: Implement IE styles key in tao.
  $ie = base_path() . path_to_theme() .'/ie.css';
  $vars['ie'] = "<!--[if lte IE 8]><style type='text/css' media='screen'>@import '{$ie}';</style><![endif]-->";

  // Help text toggler link.
  $vars['help_toggler'] = !empty($vars['help']) ? l(t('Help'), $_GET['q'], array('fragment' => 'block-atrium-help', 'attributes' => array('id' => 'help-toggler', 'class' => 'palette-toggle'))) : '';
}

/**
 * Preprocessor for theme_block().
 */
function ginkgo_preprocess_block(&$vars) {
  // If block is in a toggleable region and does not have a subject, mark it as a "widget,"
  // i.e. show its contents rather than a toggle trigger label.
  if (in_array($vars['block']->region, array('header', 'page_tools', 'space_tools'))) {
    $vars['attr']['class'] .= empty($vars['block']->subject) ? ' block-widget' : ' block-toggle';
  }
  if ($vars['block']->region === 'palette') {
    // Palette region requires module-level jQuery UI, Cookie, JSON includes.
    // Note that drupal_add_js() only works here because blocks are rendered
    // prior to the retrieval of javascript files in template_preprocess_page().
    module_exists('admin') ? drupal_add_js(drupal_get_path('module', 'admin') .'/includes/jquery.cookie.js') : '';
    module_exists('jquery_ui') ? jquery_ui_add(array('ui.draggable')) : '';
    module_exists('context_ui') ? drupal_add_js(drupal_get_path('module', 'context_ui') .'/json2.js') : '';

    // Add close button to palette region blocks.
    $vars['title'] = "<span class='close'></span>{$vars['title']}";
  }
  $vars['attr']['class'] .= empty($vars['block']->subject) ? ' block-notitle' : '';
}

/**
 * Preprocessor for theme_context_block_editable_region().
 */
function ginkgo_preprocess_context_block_editable_region(&$vars) {
  if (in_array($vars['region'], array('header', 'page_tools', 'space_tools', 'palette'))) {
    $vars['editable'] = FALSE;
  }
}

/**
 * Preprocessor for theme_help().
 */
function ginkgo_preprocess_help(&$vars) {
  $vars['layout'] = FALSE;
  $vars['links'] = '';
}

/**
 * Preprocessor for theme_node().
 */
function ginkgo_preprocess_node(&$vars) {
  if (!empty($vars['terms'])) {
    $label = t('Tagged');
    $terms = "<div class='field terms clear-block'><span class='field-label-inline-first'>{$label}:</span> {$vars['terms']}</div>";
    $vars['content'] =  $terms . $vars['content'];
  }
  $vars['title'] = check_plain($vars['node']->title);
  $vars['layout'] = FALSE;

  // Add node-page class.
  $vars['attr']['class'] .= $vars['node'] === menu_get_object() ? ' node-page' : '';

  // Don't show the full node when a comment is being previewed.
  $vars = context_get('comment', 'preview') == TRUE ? array() : $vars;

  // Clear out catchall template file suggestions like those made by og.
  // TODO refactor
  if (!empty($vars['template_files'])) {
    foreach ($vars['template_files'] as $k => $f) {
      if (strpos($f, 'node-'.$vars['type']) === FALSE) {
        unset($vars['template_files'][$k]);
      }
    }
  }
}

/**
 * Preprocessor for theme_comment().
 */
function ginkgo_preprocess_comment(&$vars) {
  // Add a time decay class.
  $decay = _ginkgo_get_comment_decay($vars['node']->nid, $vars['comment']->timestamp);
  $vars['attr']['class'] .= " decay-{$decay['decay']}";

  // If subject field not enabled, replace the title with a number.
  if (!variable_get("comment_subject_field_{$vars['node']->type}", 1)) {
    $vars['title'] = l("#{$decay['order']}", "node/{$vars['node']->nid}", array('fragment' => "comment-{$vars['comment']->cid}"));
  }

  // We're totally previewing a comment... set a context so others can bail.
  if (module_exists('context')) {
    if (empty($vars['comment']->cid) && !empty($vars['comment']->form_id)) {
      context_set('comment', 'preview', TRUE);
    }
    else if (context_isset('comment', 'preview')) {
      $vars = array();
    }
  }
}

/**
 * Preprocessor for theme_node_form().
 */
function ginkgo_preprocess_node_form(&$vars) {
  // Add node preview to top of the form if present
  $preview = theme('node_preview', NULL, TRUE);
  $vars['form']['preview'] = array('#type' => 'markup', '#weight' => -1000, '#value' => $preview);

  if (!empty($vars['form']['archive'])) {
    $vars['sidebar']['archive'] = $vars['form']['archive'];
    unset($vars['form']['archive']);
  }
}

/**
 * Function overrides =================================================
 */

/**
 * Make logo markup overridable.
 */
function ginkgo_designkit_image($name, $filepath) {
  if ($name === 'logo') {
    $title = variable_get('site_name', '');
    if (module_exists('spaces') && $space = spaces_get_space()) {
      $title = $space->title();
    }
    $url = imagecache_create_url("designkit-image-{$name}", $filepath);
    $options = array('attributes' => array('class' => 'logo', 'style' => "background-position:100% 50%; background-image:url('{$url}')"));
    return l($space->title, '<front>', $options);
  }
  return theme_designkit_image($name, $filepath);
}

/**
 * More link theme override.
 */
function ginkgo_more_link($url, $title) {
  return '<div class="more-link">'. t('<a href="@link" title="@title">View more</a>', array('@link' => check_url($url), '@title' => $title)) .'</div>';
}

/**
 * Override of theme_breadcrumb().
 */
function ginkgo_breadcrumb($breadcrumb) {
  $breadcrumb = empty($breadcrumb) ? array(l(t('Home'), '<front>')) : $breadcrumb;
  $i = 0;
  foreach ($breadcrumb as $k => $link) {
    $breadcrumb[$k] = "<span class='link link-{$i}'>{$link}</span>";
    $i++;
  }
  $breadcrumb = implode("<span class='divider'></span>", $breadcrumb);

  // Marker for this group as public or private.
  $space = spaces_get_space();
  if ($space && $space->type === 'og') {
    $attr = $space->group->og_private ?
      array('title' => t('Private'), 'class' => 'private') :
      array('title' => t('Public'), 'class' => 'public');
    $link = l('', $_GET['q'], array('attributes' => $attr));
    $breadcrumb .= "<span class='space'>{$link}</span>";
  }

  return "<div class='breadcrumb'>{$breadcrumb}</div>";
}

/**
 * Override of theme_pager(). Tao has already done the hard work for us.
 * Just exclude last/first links.
 */
function ginkgo_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 9) {
  $pager_list = theme('pager_list', $tags, $limit, $element, $parameters, $quantity);

  $links = array();
  $links['pager-previous'] = theme('pager_previous', ($tags[1] ? $tags[1] : t('Prev')), $limit, $element, 1, $parameters);
  $links['pager-next'] = theme('pager_next', ($tags[3] ? $tags[3] : t('Next')), $limit, $element, 1, $parameters);
  $pager_links = theme('links', $links, array('class' => 'links pager pager-links'));

  if ($pager_list) {
    return "<div class='pager clear-block'>$pager_list $pager_links</div>";
  }
}

/**
 * Override of theme_views_mini_pager().
 * Wrappers, tao handles the rest.
 */
function ginkgo_views_mini_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 9) {
  $tags[1] = t('Prev');
  $tags[3] = t('Next');
  $minipager = tao_views_mini_pager($tags, $limit, $element, $parameters, $quantity);
  return $minipager ? "<div class='pager minipager clear-block'>{$minipager}</div>" : '';
}

/**
 * Override of theme_node_preview().
 * We remove the teaser check / view here ... for nearly all use cases
 * this is more confusing and overbearing than anything else. We also
 * add a static variable as a trigger so that we can render node_preview
 * inside our form, rather than separate.
 */
function ginkgo_node_preview($node = NULL, $show = FALSE) {
  static $output;
  if (!isset($output) && $node) {
    $element = array(
      '#title' => t('Preview'),
      '#children' => node_view($node, 0, FALSE, 0),
      '#collapsed' => FALSE,
      '#collapsible' => TRUE,
      '#attributes' => array('class' => 'node-preview'),
    );
    $output = theme('fieldset', $element);
  }
  return $show ? $output : '';
}

/**
 * Override of theme_content_multiple_values().
 * Adds a generic wrapper.
 */
function ginkgo_content_multiple_values($element) {
  $output = theme_content_multiple_values($element);
  $field_name = $element['#field_name'];
  $field = content_fields($field_name);
  if ($field['multiple'] >= 1) {
    return "<div class='content-multiple-values'>{$output}</div>";
  }
  return $output;
}

/**
 * Override of theme('node_submitted').
 */
function ginkgo_node_submitted($node) {
  $byline = theme('username', $node);
  $date = module_exists('reldate') ? reldate_format_date($node->created) : format_date($node->created, 'small');
  return "<div class='byline'>{$byline}</div><div class='date'>$date</div>";
}

/**
 * Override of theme('comment_submitted').
 */
function ginkgo_comment_submitted($comment) {
  $comment->created = $comment->timestamp;
  return ginkgo_node_submitted($comment);
}


/**
 * Preprocessor for theme('views_view_fields').
 */
function ginkgo_preprocess_views_view_fields(&$vars) {
  foreach ($vars['fields'] as $field) {
    if ($class = _ginkgo_get_views_field_class($field->handler)) {
      $field->class = $class;
    }
  }

  // Write this as a row plugin to allow modules/features to define this stuff.
  if (get_class($vars['view']->style_plugin) == 'views_plugin_style_list') {
    $enable_grouping = TRUE;

    // Override arrays for grouping
    $view_id = "{$vars['view']->name}:{$vars['view']->current_display}";
    $overrides = array(
      "profile_display:page_1" => array(),
      "blog_comments:block_1" => array(
        'meta' => array('date', 'user-picture', 'username', 'author'),
      ),
    );
    if (isset($overrides[$view_id])) {
      $groups = $overrides[$view_id];
    }
    else {
      $groups = array(
        'meta' => array('date', 'user-picture', 'username', 'related-title', 'author'),
        'admin' => array('edit', 'delete'),
      );
    }

    foreach ($vars['fields'] as $id => $field) {
      $found = FALSE;
      foreach ($groups as $group => $valid_fields) {
        if (in_array($field->class, $valid_fields)) {
          $grouped[$group][$id] = $field;
          $found = TRUE;
          break;
        }
      }
      if (!$found) {
        $grouped['content'][$id] = $field;
      }
    }

    // If the listing doesn't have any fields that will be grouped
    // fallback to default (non-grouped) formatting.
    $enable_grouping = count($grouped) <= 1 ? FALSE : TRUE;
    $vars['classes'] = isset($vars['classes']) ? $vars['classes'] : '';
    foreach (array_keys($grouped) as $group) {
      $vars['classes'] .= " grouping-{$group}";
    }
  }
  else {
    $enable_grouping = FALSE;
    $grouped = array('content' => $vars['fields']);
  }
  $vars['enable_grouping'] = $enable_grouping;
  $vars['grouped'] = $grouped;
}

/**
 * Preprocessor for theme('views_view_table').
 */
function ginkgo_preprocess_views_view_table(&$vars) {
  $view = $vars['view'];
  foreach ($view->field as $field => $handler) {
    if (isset($vars['fields'][$field]) && $class = _ginkgo_get_views_field_class($handler)) {
      $vars['fields'][$field] = $class;
    }
  }
}

/**
 * Helper function to get the appropriate class name for Views field.
 */
function _ginkgo_get_views_field_class($handler) {
  $handler_class = get_class($handler);
  $search = array(
    'project' => 'project',
    'priority' => 'priority',
    'status' => 'status',

    'history_user' => 'new',

    'date' => 'date',
    'timestamp' => 'date',

    'user_picture' => 'user-picture',
    'username' => 'username',
    'name' => 'username',

    'markup' => 'markup',
    'xss' => 'markup',

    'spaces_feature' => 'feature',
    'group_nids' => 'group',

    'numeric' => 'number',
    'count' => 'count',

    'edit' => 'edit',
    'delete' => 'delete',
  );
  foreach ($search as $needle => $class) {
    if (strpos($handler_class, $needle) !== FALSE) {
      return $class;
    }
  }
  // Fallback
  if (!empty($handler->relationship) && ($handler->view->base_table !== 'users')) {
    return "related-{$handler->field}";
  }
  return $handler->field;
}

/**
 * Return both an order (e.g. #1 for oldest to #n for the nth comment)
 * and a decay value (0 for newest, 10 for oldest) for a given comment.
 */
function _ginkgo_get_comment_decay($nid, $timestamp) {
  static $timerange;
  if (!isset($timerange[$nid])) {
    $range = array();
    $result = db_query("SELECT timestamp FROM {comments} WHERE nid = %d ORDER BY timestamp ASC", $nid);
    $i = 1;
    while ($row = db_fetch_object($result)) {
      $timerange[$nid][$row->timestamp] = $i;
      $i++;
    }
  }
  if (!empty($timerange[$nid][$timestamp])) {
    $decay = max(array_keys($timerange[$nid])) - min(array_keys($timerange[$nid]));
    $decay = $decay > 0 ? ((max(array_keys($timerange[$nid])) - $timestamp) / $decay) : 0;
    $decay = floor($decay * 10);
    return array('order' => $timerange[$nid][$timestamp], 'decay' => $decay);
  }
  return array('order' => 1, 'decay' => 0);
}


/**
 * Generate an icon class from a path.
 */
function _ginkgo_icon_class($path) {
  $path = drupal_get_path_alias($path);
  return str_replace('/', '-', $path);
}
