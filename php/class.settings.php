<?php
if(!class_exists('wow_armory_table_settings'))
{
    class wow_armory_table_settings
    {
        private $options;

        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function enqueue_scripts()
        {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wow-armory-backend', plugins_url('../js/jquery.wowarmory.backend.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
        }

        public function add_plugin_page()
        {
            add_submenu_page( 'wow-armory-table', __("Settings", "wowarmorytable-plugin"), __("Settings", "wowarmorytable-plugin"), 'manage_options', 'wow-armory-table-options', array( $this, 'create_admin_page' ) );
        }

        public function create_admin_page()
        {
            $this->options = get_option( 'wow_arsenal_table_options' );
            ?>
            <div class="wrap">
                <h2><?php _e("WoW Armory Table - Settings", "wowarmorytable-plugin"); ?></h2>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'wow_arsenal_main_options_group' );
                    do_settings_sections( 'wow-arsenal-settings-admin' );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php
        }

        public function page_init()
        {
            register_setting(
                'wow_arsenal_main_options_group',
                'wow_arsenal_table_options',
                array( $this, 'sanitize' )
            );

            add_settings_section(
                'wow_arsenal_main_section',
                __("Base Settings", "wowarmorytable-plugin"),
                array( $this, 'print_section_info' ),
                'wow-arsenal-settings-admin'
            );

            add_settings_field(
                'region',
                __("Region", "wowarmorytable-plugin"),
                array( $this, 'region_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'server',
                __("Server", "wowarmorytable-plugin"),
                array( $this, 'server_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'guildname',
                __("Guild Name", "wowarmorytable-plugin"),
                array( $this, 'guildname_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'language',
                __("Armory Language", "wowarmorytable-plugin"),
                array( $this, 'language_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'entries',
                __("Entries to show on the table", "wowarmorytable-plugin"),
                array( $this, 'entriesamount_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'ranks',
                __("Guild Ranks", "wowarmorytable-plugin"),
                array( $this, 'guildranks_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_field(
                'maxWidth',
                __("Class display max. columns", "wowarmorytable-plugin"),
                array( $this, 'maxwidth_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_main_section'
            );

            add_settings_section(
                'wow_arsenal_style_section',
                __("Style Settings", "wowarmorytable-plugin"),
                array( $this, 'print_section_style' ),
                'wow-arsenal-settings-admin'
            );

            add_settings_field(
                'classcolors',
                __("Class Colors", "wowarmorytable-plugin"),
                array( $this, 'class_colors_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_style_section'
            );

            add_settings_field(
                'normaltablecolors',
                __("Normal View Colors", "wowarmorytable-plugin"),
                array( $this, 'normal_table_colors_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_style_section'
            );

            add_settings_field(
                'classtablecolors',
                __("Class View Colors", "wowarmorytable-plugin"),
                array( $this, 'class_table_colors_callback' ),
                'wow-arsenal-settings-admin',
                'wow_arsenal_style_section'
            );
        }

        public function sanitize( $input )
        {
            $new_input = array();

            if( isset( $input['guildname'] ) )
                $new_input['guildname'] = sanitize_text_field( $input['guildname'] );

            if( isset( $input['server'] ) )
                $new_input['server'] = sanitize_text_field( $input['server'] );

            if( isset( $input['region'] ) )
                $new_input['region'] = sanitize_text_field( $input['region'] );

            if( isset( $input['language'] ) )
                $new_input['language'] = sanitize_text_field( $input['language'] );

            if( isset( $input['maxWidth'] ) )
                $new_input['maxWidth'] = sanitize_text_field( $input['maxWidth'] );

            if( isset( $input['ranks'] ) )
            {
                $new_input['ranks'] = $input['ranks'];
            }

            if( isset( $input['entries'] ) )
            {
                $new_input['entries'] = $input['entries'];
            }

            if( isset ($input['classcolors'] ) )
            {
                $new_input['classcolors'] = $input['classcolors'];
            }

            if( isset ($input['normaltablecolors'] ) )
            {
                $new_input['normaltablecolors'] = $input['normaltablecolors'];
            }

            if( isset ($input['classtablecolors'] ) )
            {
                $new_input['classtablecolors'] = $input['classtablecolors'];
            }

            return $new_input;
        }

        public function print_section_info()
        {

        }

        public function print_section_style()
        {

        }

        public function class_colors_callback()
        {
            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][0]" id="classcolors[0]" class="classcolors" value="%s" data-default-color="#C79C6E" /><br />',
                isset( $this->options['classcolors'][0] ) ? esc_attr( $this->options['classcolors'][0]) : '#C79C6E');
            echo '<p class="description">'.__("Warrior", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][1]" id="classcolors[1]" class="classcolors" value="%s" data-default-color="#F58CBA" /><br />',
                isset( $this->options['classcolors'][1] ) ? esc_attr( $this->options['classcolors'][1]) : '#F58CBA');
            echo '<p class="description">'.__("Paladin", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][2]" id="classcolors[2]" class="classcolors" value="%s" data-default-color="#ABD473" /><br />',
                isset( $this->options['classcolors'][2] ) ? esc_attr( $this->options['classcolors'][2]) : '#ABD473');
            echo '<p class="description">'.__("Hunter", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][3]" id="classcolors[3]" class="classcolors" value="%s" data-default-color="#FFF569" /><br />',
                isset( $this->options['classcolors'][3] ) ? esc_attr( $this->options['classcolors'][3]) : '#FFF569');
            echo '<p class="description">'.__("Rogue", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][4]" id="classcolors[4]" class="classcolors" value="%s" data-default-color="#FFFFFF" /><br />',
                isset( $this->options['classcolors'][4] ) ? esc_attr( $this->options['classcolors'][4]) : '#FFFFFF');
            echo '<p class="description">'.__("Priest", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][5]" id="classcolors[5]" class="classcolors" value="%s" data-default-color="#C41F3B" /><br />',
                isset( $this->options['classcolors'][5] ) ? esc_attr( $this->options['classcolors'][5]) : '#C41F3B');
            echo '<p class="description">'.__("Death Knight", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][6]" id="classcolors[6]" class="classcolors" value="%s" data-default-color="#0070DE" /><br />',
                isset( $this->options['classcolors'][6] ) ? esc_attr( $this->options['classcolors'][6]) : '#0070DE');
            echo '<p class="description">'.__("Shaman", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][7]" id="classcolors[7]" class="classcolors" value="%s" data-default-color="#69CCF0" /><br />',
                isset( $this->options['classcolors'][7] ) ? esc_attr( $this->options['classcolors'][7]) : '#69CCF0');
            echo '<p class="description">'.__("Mage", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][8]" id="classcolors[8]" class="classcolors" value="%s" data-default-color="#9482C9" /><br />',
                isset( $this->options['classcolors'][8] ) ? esc_attr( $this->options['classcolors'][8]) : '#9482C9');
            echo '<p class="description">'.__("Warlock", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][9]" id="classcolors[9]" class="classcolors" value="%s" data-default-color="#00FF96" /><br />',
                isset( $this->options['classcolors'][9] ) ? esc_attr( $this->options['classcolors'][9]) : '#00FF96');
            echo '<p class="description">'.__("Monk", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classcolors][10]" id="classcolors[10]" class="classcolors" value="%s" data-default-color="#FF7D0A" /><br />',
                isset( $this->options['classcolors'][10] ) ? esc_attr( $this->options['classcolors'][10]) : '#FF7D0A');
            echo '<p class="description">'.__("Druid", "wowarmorytable-plugin").'</p><br />';
        }

        public function normal_table_colors_callback()
        {
            printf(
                '<input type="text" name="wow_arsenal_table_options[normaltablecolors][0]" id="normaltablecolors[0]" class="normaltablecolors" value="%s" data-default-color="#202020" /><br />',
                isset( $this->options['normaltablecolors'][0] ) ? esc_attr( $this->options['normaltablecolors'][0]) : '#202020');
            echo '<p class="description">'.__("Odd Row", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[normaltablecolors][1]" id="normaltablecolors[1]" class="normaltablecolors" value="%s" data-default-color="#3b3b3b" /><br />',
                isset( $this->options['normaltablecolors'][1] ) ? esc_attr( $this->options['normaltablecolors'][1]) : '#3b3b3b');
            echo '<p class="description">'.__("Even Row", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[normaltablecolors][2]" id="normaltablecolors[2]" class="normaltablecolors" value="%s" data-default-color="#4c4c4c" /><br />',
                isset( $this->options['normaltablecolors'][2] ) ? esc_attr( $this->options['normaltablecolors'][2]) : '#4c4c4c');
            echo '<p class="description">'.__("Border Color", "wowarmorytable-plugin").'</p><br />';
        }

        public function class_table_colors_callback()
        {
            printf(
                '<input type="text" name="wow_arsenal_table_options[classtablecolors][0]" id="classtablecolors[0]" class="classtablecolors" value="%s" data-default-color="" /><br />',
                isset( $this->options['classtablecolors'][0] ) ? esc_attr( $this->options['classtablecolors'][0]) : '');
            echo '<p class="description">'.__("Odd Row", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classtablecolors][1]" id="classtablecolors[1]" class="classtablecolors" value="%s" data-default-color="" /><br />',
                isset( $this->options['classtablecolors'][1] ) ? esc_attr( $this->options['classtablecolors'][1]) : '');
            echo '<p class="description">'.__("Even Row", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classtablecolors][2]" id="classtablecolors[2]" class="classtablecolors" value="%s" data-default-color="" /><br />',
                isset( $this->options['classtablecolors'][2] ) ? esc_attr( $this->options['classtablecolors'][2]) : '');
            echo '<p class="description">'.__("Font Color", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classtablecolors][3]" id="classtablecolors[3]" class="classtablecolors" value="%s" data-default-color="" /><br />',
                isset( $this->options['classtablecolors'][3] ) ? esc_attr( $this->options['classtablecolors'][3]) : '');
            echo '<p class="description">'.__("Overall Background", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[classtablecolors][4]" id="classtablecolors[4]" class="classtablecolors" value="%s" data-default-color="" /><br />',
                isset( $this->options['classtablecolors'][4] ) ? esc_attr( $this->options['classtablecolors'][4]) : '');
            echo '<p class="description">'.__("Divider Color (hr)", "wowarmorytable-plugin").'</p><br />';

        }

        public function region_callback()
        {
            echo '<select name="wow_arsenal_table_options[region]" id="region">';
            echo '<option ';
            if ( $this->options["region"] == "" )  echo "selected='selected'";
            echo ' value="">'.__("Choose...", "wowarmorytable-plugin").'</option>';
            echo '<option ';
            if ( $this->options["region"] == "us" )  echo "selected='selected'";
            echo 'value="us">US</option>';
            echo '<option ';
            if ( $this->options["region"] == "eu" )  echo "selected='selected'";
            echo 'value="eu">EU</option>';
            echo '<option ';
            if ( $this->options["region"] == "tw" )  echo "selected='selected'";
            echo 'value="tw">TW</option>';
            echo '<option ';
            if ( $this->options["region"] == "kr" )  echo "selected='selected'";
            echo 'value="kr">KR</option>';
            echo '</select>';
        }

        public function language_callback()
        {
            echo '<select name="wow_arsenal_table_options[language]" id="language">';
            echo '<option ';
            if ( $this->options["language"] == "" )  echo "selected='selected'";
            echo ' value="">'.__("Choose...", "wowarmorytable-plugin").'</option>';
            echo '<option ';
            if ( $this->options["language"] == "en" )  echo "selected='selected'";
            echo 'value="en">English</option>';
            echo '<option ';
            if ( $this->options["language"] == "de" )  echo "selected='selected'";
            echo 'value="de">German</option>';
            echo '<option ';
            if ( $this->options["language"] == "es" )  echo "selected='selected'";
            echo 'value="es">Espanol</option>';
            echo '<option ';
            if ( $this->options["language"] == "pt" )  echo "selected='selected'";
            echo 'value="pt">Portugues</option>';
            echo '<option ';
            if ( $this->options["language"] == "fr" )  echo "selected='selected'";
            echo 'value="fr">Francais</option>';
            echo '<option ';
            if ( $this->options["language"] == "it" )  echo "selected='selected'";
            echo 'value="it">Italiano</option>';
            echo '<option ';
            if ( $this->options["language"] == "ru" )  echo "selected='selected'";
            echo 'value="ru">Russian</option>';
            echo '<option ';
            if ( $this->options["language"] == "ko" )  echo "selected='selected'";
            echo 'value="ko">Korean</option>';
            echo '</select>';
        }

        public function entriesamount_callback()
        {
            echo '<select name="wow_arsenal_table_options[entries]" id="entries">';
            echo '<option ';
            if ( $this->options["entries"] == "10" )  echo "selected='selected'";
            echo 'value="10">10</option>';
            echo '<option ';
            if ( $this->options["entries"] == "25" )  echo "selected='selected'";
            echo 'value="25">25</option>';
            echo '<option ';
            if ( $this->options["entries"] == "50" )  echo "selected='selected'";
            echo 'value="50">50</option>';
            echo '<option ';
            if ( $this->options["entries"] == "100" )  echo "selected='selected'";
            echo 'value="100">100</option>';
            echo '</select>';
        }

        public function guildranks_callback()
        {
            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][0]" id="ranks[0]" value="%s" /><br />',
                isset( $this->options['ranks'][0] ) ? esc_attr( $this->options['ranks'][0]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 0 - '.__("Highest Rank, like Guild master", "wowarmorytable-plugin").'</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][1]" id="ranks[1]" value="%s" /><br />',
                isset( $this->options['ranks'][1] ) ? esc_attr( $this->options['ranks'][1]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 1.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][2]" id="ranks[2]" value="%s" /><br />',
                isset( $this->options['ranks'][2] ) ? esc_attr( $this->options['ranks'][2]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 2.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][3]" id="ranks[3]" value="%s" /><br />',
                isset( $this->options['ranks'][3] ) ? esc_attr( $this->options['ranks'][3]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 3.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][4]" id="ranks[4]" value="%s" /><br />',
                isset( $this->options['ranks'][4] ) ? esc_attr( $this->options['ranks'][4]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 4.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][5]" id="ranks[5]" value="%s" /><br />',
                isset( $this->options['ranks'][5] ) ? esc_attr( $this->options['ranks'][5]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 5.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][6]" id="ranks[6]" value="%s" /><br />',
                isset( $this->options['ranks'][6] ) ? esc_attr( $this->options['ranks'][6]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 6.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][7]" id="ranks[7]" value="%s" /><br />',
                isset( $this->options['ranks'][7] ) ? esc_attr( $this->options['ranks'][7]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 7.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][8]" id="ranks[8]" value="%s" /><br />',
                isset( $this->options['ranks'][8] ) ? esc_attr( $this->options['ranks'][8]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 8.</p><br />';

            printf(
                '<input type="text" name="wow_arsenal_table_options[ranks][9]" id="ranks[9]" value="%s" /><br />',
                isset( $this->options['ranks'][9] ) ? esc_attr( $this->options['ranks'][9]) : '');
            echo '<p class="description">'.__("Rank", "wowarmorytable-plugin").' 9 - '.__("Lowest Rank", "wowarmorytable-plugin").'.</p><br />';
        }

        public function guildname_callback()
        {
            printf(
                '<input type="text" id="guildname" name="wow_arsenal_table_options[guildname]" value="%s" />',
                isset( $this->options['guildname'] ) ? esc_attr( $this->options['guildname']) : ''
            );
        }

        public function maxwidth_callback()
        {
            printf(
                '<input type="text" id="maxWidth" name="wow_arsenal_table_options[maxWidth]" value="%s" />',
                isset( $this->options['maxWidth'] ) ? esc_attr( $this->options['maxWidth']) : ''
            );
        }

        public function server_callback()
        {
            printf(
                '<input type="text" id="server" name="wow_arsenal_table_options[server]" value="%s" />',
                isset( $this->options['server'] ) ? esc_attr( $this->options['server']) : ''
            );
        }
    }
}
?>