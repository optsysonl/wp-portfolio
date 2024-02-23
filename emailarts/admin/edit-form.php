<?php

// don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

function wpea_admin_save_button($post_id)
{
    static $button = '';

    if (!empty($button)) {
        echo $button;
        return;
    }

    $nonce = wp_create_nonce('wpea-save-form_' . $post_id);

    $onclick = sprintf(
        "this.form._wpnonce.value = '%s';"
        . " this.form.action.value = 'save';"
        . " return true;",
        $nonce);

    $button = sprintf(
        '<input type="submit" class="button-primary" name="wpea-save" value="%1$s" onclick="%2$s" />',
        esc_attr(__('Save', 'emailarts')),
        $onclick);

    echo $button;
}

?>
    <div class="wrap" id="wpea-form-editor">

        <h1 class="wp-heading-inline"><?php

            if ($post->initial()) {
                echo esc_html(__('Add New Form', 'emailarts'));
            } else {
                echo esc_html(__('Edit Form', 'emailarts'));
            }
            ?></h1>

        <?php

        if (!$post->initial()
            and current_user_can('wpea_edit_forms')) {
            echo wpea_link(
                menu_page_url('wpea-new', false),
                __('Add New', 'emailarts'),
                array('class' => 'page-title-action')
            );
        }
        ?>

        <hr class="wp-header-end">

        <?php
        do_action('wpea_admin_warnings',
            $post->initial() ? 'wpea-new' : 'wpea',
            wpea_current_action(),
            $post
        );

        do_action('wpea_admin_notices',
            $post->initial() ? 'wpea-new' : 'wpea',
            wpea_current_action(),
            $post
        );
        ?>

        <?php

        if ($post) :

            $disabled = '';

            ?>

            <form method="post"
                  action="<?php echo esc_url(add_query_arg(array('post' => $post_id), menu_page_url('wpea', false))); ?>"
                  id="wpea-admin-form-element"<?php do_action('wpea_post_edit_form_tag'); ?>>
                <?php
                wp_nonce_field('wpea-save-form_' . $post_id);
                ?>
                <input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int)$post_id; ?>"/>

                <input type="hidden" id="wpea-locale" name="wpea-locale"
                       value="<?php //echo esc_attr($post->locale());
                       ?>"/>

                <input type="hidden" id="hiddenaction" name="action" value="save"/>
                <input type="hidden" id="active-tab" name="active-tab"
                       value="<?php echo isset($_GET['active-tab']) ? (int)$_GET['active-tab'] : '0'; ?>"/>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div id="titlediv">
                                <div id="titlewrap">
                                    <label class="screen-reader-text" id="title-prompt-text"
                                           for="title"><?php echo esc_html(__('Enter title here', 'emailarts')); ?></label>
                                    <?php
                                    $posttitle_atts = array(
                                        'type' => 'text',
                                        'name' => 'post_title',
                                        'size' => 30,
                                        'value' => $post->initial() ? '' : $post->title(),
                                        'id' => 'title',
                                        'spellcheck' => 'true',
                                        'autocomplete' => 'off'
                                    );

                                    echo sprintf('<input %s />', wpea_format_atts($posttitle_atts));
                                    ?>
                                </div><!-- #titlewrap -->

                                <div class="inside">
                                    <?php
                                    if (!$post->initial()) :
                                        ?>
                                        <p class="description">
                                            <label for="wpea-shortcode"><?php echo esc_html(__("Copy this shortcode and paste it into your post, page, or text widget content:", 'emailarts')); ?></label>
                                            <span class="shortcode wp-ui-highlight"><input type="text"
                                                                                           id="wpea-shortcode"
                                                                                           onfocus="this.select();"
                                                                                           readonly="readonly"
                                                                                           class="large-text code"
                                                                                           value="<?php echo esc_attr($post->shortcode()); ?>"/></span>
                                        </p>
                                        <?php
                                        if ($old_shortcode = $post->shortcode(array('use_old_format' => true))) :
                                            ?>
                                            <p class="description">
                                                <label for="wpea-shortcode-old"><?php echo esc_html(__("You can also use this old-style shortcode:", 'emailarts')); ?></label>
                                                <span class="shortcode old"><input type="text" id="wpea-shortcode-old"
                                                                                   onfocus="this.select();"
                                                                                   readonly="readonly"
                                                                                   class="large-text code"
                                                                                   value="<?php echo esc_attr($old_shortcode); ?>"/></span>
                                            </p>
                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                </div>
                            </div><!-- #titlediv -->
                        </div><!-- #post-body-content -->

                        <div id="postbox-container-1" class="postbox-container">
                            <div id="submitdiv" class="postbox">
                                <h3><?php echo esc_html(__('Status', 'emailarts')); ?></h3>
                                <div class="inside">
                                    <div class="submitbox" id="submitpost">

                                        <div id="minor-publishing-actions">

                                            <div class="hidden">
                                                <input type="submit" class="button-primary" name="wpea-save"
                                                       value="<?php echo esc_attr(__('Save', 'emailarts')); ?>"/>
                                            </div>

                                            <?php
                                            if (!$post->initial()) :
                                                $copy_nonce = wp_create_nonce('wpea-copy-form_' . $post_id);
                                                ?>
                                                <input type="submit" name="wpea-copy" class="copy button"
                                                       value="<?php echo esc_attr(__('Duplicate', 'emailarts')); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
                                            <?php endif; ?>
                                        </div><!-- #minor-publishing-actions -->

                                        <div id="misc-publishing-actions">
                                            <?php do_action('wpea_admin_misc_pub_section', $post_id); ?>
                                        </div><!-- #misc-publishing-actions -->

                                        <div id="major-publishing-actions">

                                            <?php
                                            if (!$post->initial()) :
                                                $delete_nonce = wp_create_nonce('wpea-delete-form_' . $post_id);
                                                ?>
                                                <div id="delete-action">
                                                    <input type="submit" name="wpea-delete"
                                                           class="delete submitdelete"
                                                           value="<?php echo esc_attr(__('Delete', 'emailarts')); ?>" <?php echo "onclick=\"if (confirm('" . esc_js(__("You are about to delete this Form.\n  'Cancel' to stop, 'OK' to delete.", 'emailarts')) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
                                                </div><!-- #delete-action -->
                                            <?php endif; ?>

                                            <div id="publishing-action">
                                                <span class="spinner"></span>
                                                <?php wpea_admin_save_button($post_id); ?>
                                            </div>
                                            <div class="clear"></div>
                                        </div><!-- #major-publishing-actions -->
                                    </div><!-- #submitpost -->
                                </div>
                            </div>

                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <div id="form-editor">

                                <?php

                                $editor = new WPEA_Editor($post);
                                $panels = array();
                                $panels = array(
                                    'available_fields' => array(
                                        'title' => __('Settings', 'emailarts'),
                                        'callback' => 'wpea_editor_panel_fields',
                                    ),
                                    'form-panel' => array(
                                        'title' => __('Form', 'emailarts'),
                                        'callback' => 'wpea_editor_panel_form',
                                    ),
//                                    'messages-panel' => array(
//                                        'title' => __('Messages', 'emailarts'),
//                                        'callback' => 'wpea_editor_panel_messages',
//                                    ),
                                );

//                                $additional_settings = $post->prop('additional_settings');
//
//                                if (!is_scalar($additional_settings)) {
//                                    $additional_settings = '';
//                                }
//
//                                $additional_settings = trim($additional_settings);
//                                $additional_settings = explode("\n", $additional_settings);
//                                $additional_settings = array_filter($additional_settings);
//                                $additional_settings = count($additional_settings);

                                //                                }

                                $panels = apply_filters('wpea_editor_panels', $panels);

                                foreach ($panels as $id => $panel) {
                                    $editor->add_panel($id, $panel['title'], $panel['callback']);
                                }

                                $editor->display();
                                ?>
                            </div>
                            <p class="submit"><?php wpea_admin_save_button($post_id); ?></p>

                        </div>

                    </div>
                    <br class="clear"/>
                </div>
            </form>

        <?php endif; ?>

    </div>

<?php

$tag_generator = WPEA_TagGenerator::get_instance();
$tag_generator->print_panels($post);

do_action('wpea_admin_footer', $post);
