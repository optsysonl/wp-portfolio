<?php

class WPEA_Editor
{

    private $form;
    private $panels = array();

    public function __construct(EmailArts $form)
    {
        $this->form = $form;
    }

    public function add_panel($panel_id, $title, $callback)
    {
        if (wpea_is_name($panel_id)) {
            $this->panels[$panel_id] = array(
                'title' => $title,
                'callback' => $callback,
            );
        }
    }

    public function display()
    {
        if (empty($this->panels)) {
            return;
        }
        $available_fields = $this->form->available_fields;
        echo '<ul id="form-editor-tabs">';

        foreach ($this->panels as $panel_id => $panel) {
            $tabs_classes = ($this->form->list_id && !empty($available_fields)) ? 'form-editor-tab' : 'form-editor-tab disabled';
            $classes = ($panel_id == 'available_fields') ? 'form-editor-tab active' : $tabs_classes;
            echo sprintf(
                '<li class="'.$classes.'" data-id="%1$s">%2$s</li>',
                esc_attr($panel_id),
                esc_html($panel['title'])
            );
        }

        echo '</ul>';

        foreach ($this->panels as $panel_id => $panel) {
            $classes = ($panel_id == 'available_fields') ? 'form-editor-panel displayed' : 'form-editor-panel';
            echo sprintf(
                '<div class="'.$classes.'" id="%1$s">',
                esc_attr($panel_id)
            );

            if (is_callable($panel['callback'])) {
                $this->notice($panel_id, $panel);
                call_user_func($panel['callback'], $this->form);
            }

            echo '</div>';
        }

    }

    public function notice($panel_id, $panel)
    {
        echo '<div class="config-error"></div>';
    }
}

function wpea_editor_panel_form($post)
{
    $description = __("", 'emailarts');

    $available_fields = $post->available_fields;
    $tag_generator = WPEA_TagGenerator::get_instance();
    foreach($available_fields as $key=>$field) {
        $required = ($field['required'] == 'yes') ? true : false;
        $tag_generator->add($key, $field['type'], __($field['label'], 'emailarts'), 'wpea_tag_generator_text',[], $required);
    }
    $tag_generator->add( 'submit','submit', __( 'submit', 'emailarts' ), 'wpea_tag_generator_submit', array( 'nameless' => 1 ), false );

    ?>

    <h2><?php echo esc_html(__('Form', 'emailarts')); ?></h2>

    <fieldset style="padding: 0 10px;">
        <legend><?php echo $description; ?></legend>
        <p><b>Available fields</b></p>
        <?php
        $tag_generator = WPEA_TagGenerator::get_instance();
        $tag_generator->print_buttons();

        ?>
        <p><i>Fields with <strong>*</strong> are required</i></p>
        <div style="display: flex; justify-content: flex-end">
            <button type="button" class="button" style="margin-bottom: 10px;" id="generate_form">Create form from available fields</button>
        </div>
        <textarea id="wpea-form" name="wpea-form" cols="100" rows="24" class="large-text code"
                  data-config-field="form.body"><?php echo esc_textarea($post->prop('form')); ?></textarea>
    </fieldset>
    <?php
}

function wpea_editor_panel_fields($post){
    $settings = get_option('WPEmailArts_settings');
    if ($settings !==  null) {
        $settings = unserialize($settings);
    }

    ?>

    <h2><?php echo esc_html(__('Choose List', 'emailarts')); ?></h2>

    <?php
    if(isset($settings['api-connection-status'])){

        $available_fields = $post->available_fields;

        $selected_list_id = $post->list_id;
        $selected_fields = (!empty($available_fields))? $available_fields : [];
        $fieldName = 'wpea-available-fields';

        $oldSdkConfig = MailWizzApi_Base::getConfig();
        MailWizzApi_Base::setConfig(mwznb_build_sdk_config($settings['publicKey'], $settings['privateKey']));

        $endpoint = new MailWizzApi_Endpoint_Lists();
        $response = $endpoint->getLists();
        $response = $response->body->toArray();

        mwznb_restore_sdk_config($oldSdkConfig);
        unset($oldSdkConfig);

        $lists = $response['data']['records'];

        $list_id_chosen_html = (isset($selected_list_id) && $selected_list_id !== "0") ? '' : 'style="display: none;"';

        ?>
        <div style="padding: 8px 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <select id="api-list" name="list_ID">
                    <option value="0">Choose list</option>
                    <?php
                    foreach($lists as $item){
                        ?>
                        <option
                                value="<?php echo $item['general']['list_uid']?>"
                                <?php echo ($item['general']['list_uid'] == $selected_list_id) ? 'selected="selected"' : ''; ?>
                        >
                            <?php echo $item['general']['name']; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <button type="button" id="button-apply-list-changes" class="button-primary" style="display: none;">Apply changes</button>
            </div>

            <p><i>Select one of the available list.</i></p>
        </div>

        <?php

        ?>

        <div class="form-configuration-step-2" <?php echo $list_id_chosen_html; ?>>

            <h2><?php echo esc_html(__('Available Fields', 'emailarts')); ?></h2>

            <div  style="padding: 8px 12px;" id="form-configuration-step-2-fields">
                <?php
                $available_fields = $post->available_fields;
                if(isset($selected_list_id) && $selected_list_id !== "0" ){
                    if(isset($available_fields) && !empty($available_fields )){
                        mwznb_build_fields_table($available_fields, $fieldName, $selected_fields);
                    }else {
                        $oldSdkConfig = MailWizzApi_Base::getConfig();
                        MailWizzApi_Base::setConfig(mwznb_build_sdk_config($settings['publicKey'], $settings['privateKey']));

                        $endpoint = new MailWizzApi_Endpoint_ListFields();
                        $response = $endpoint->getFields($selected_list_id);
                        $response = $response->body->toArray();

                        mwznb_restore_sdk_config($oldSdkConfig);
                        unset($oldSdkConfig);

                        mwznb_generate_fields_table((array)$response['data']['records'], $fieldName, $selected_fields);
                    }
                }

                ?>
            </div>

            <p style="padding: 8px 12px;"><i>Select which fields will be shown in your form.</i></p>

        </div>
        <?php
    }
}
