<?php
/**
 * Plugin Name: World of Warcraft - Armory Table
 * Plugin URI: http://kilo-moana.com/world-warcraft-armory-table/
 * Description: Simple Plugin to display you guild characters in a table.
 * Version: 0.3.2
 * Author: Alexander Siemer-Schmetzke
 * Author URI: http://kilo-moana.com/
 * Text Domain: wowarmorytable-plugin
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


class wow_armory_table
{
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'wow_armory_table_install'));
        register_deactivation_hook(__FILE__ , array($this, 'wow_armory_table_uninstall') );

        add_action('plugins_loaded', array($this, 'wowarmorytable_init'));

        add_action( 'init_custom_menu_separator', array($this, 'add_wow_armory_table_menu_separator') );
        add_action( 'init', array($this, 'set_wow_armory_table_menu_separator') );

        add_action( 'admin_menu', array($this, 'wow_armory_table_menu_page'));
        add_action('wp_footer', array($this, 'custom_style'), 100);

        require_once(sprintf("%s/php/class.settings.php", dirname(__FILE__)));

        if( is_admin() )
            new wow_armory_table_settings();

        require_once(sprintf("%s/php/class.characters.php", dirname(__FILE__)));

        new wow_armory_table_characters();

        add_shortcode('wow-arsenal-progress', array($this, 'display_guild_progress'));
        add_shortcode('wow-char', array($this, 'get_single_character'));
    }

    function wowarmorytable_init() {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'wowarmorytable-plugin', false, $plugin_dir.'/lang/' );
    }

    public function custom_style()
    {
        $options = get_option('wow_arsenal_table_options');
        echo '<style>';
        for($i = 0;$i < count($options['classcolors']);$i++)
        {
            echo 'a.wowclass-'.($i+1).' {';
            echo 'color: '.$options['classcolors'][$i].'!important';
            echo '}';
        }

        echo '</style>';

    }

    function wow_armory_table_install()
    {
        global $wpdb;

        $table = $wpdb->base_prefix . "wow_armory_table_chars";
        $structure = "CREATE TABLE $table (
	        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  		name VARCHAR(255) UNIQUE,
			lvl INT(10) NOT NULL,
	  		race VARCHAR(255) NOT NULL,
	  		class VARCHAR(255) NOT NULL,
			achievementpoints INT(10) NOT NULL,
			gender int(10) NOT NULL,
			rank INT(10) NOT NULL
			) ENGINE = MYISAM, CHARACTER SET utf8 COLLATE utf8_bin";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_classes";
        $structure = "CREATE TABLE $table (
	        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  		name VARCHAR(255) UNIQUE,
	  		powerType VARCHAR(255) NOT NULL,
	  		mask INT(10) NOT NULL,
	  		map INT(10) NOT NULL
	  		) ENGINE = MYISAM, CHARACTER SET utf8 COLLATE utf8_bin;";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_groups";
        $structure = "CREATE TABLE $table (
	        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  		name VARCHAR(255) UNIQUE,
	  		groupOrder INT(10) NOT NULL
	  		) ENGINE = MYISAM, CHARACTER SET utf8 COLLATE utf8_bin;";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_groups_mapping";
        $structure = "CREATE TABLE $table (
	        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  		characterId INT(10) NOT NULL,
	  		groupId INT(10) NOT NULL,
	  		groupRole VARCHAR(255) NOT NULL
	  		) ENGINE = MYISAM, CHARACTER SET utf8 COLLATE utf8_bin;";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_progress_mapping";
        $structure = "CREATE TABLE $table (
	        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  		characterId INT(10) NOT NULL,
	  		raidId INT(10) NOT NULL,
	  		raidName VARCHAR(255) NOT NULL,
	  		normalRaid INT(10) NOT NULL,
	  		heroicRaid INT(10) NOT NULL,
	  		bossId INT(10) NOT NULL,
	  		normalBoss INT(10) NOT NULL,
	  		heroicBoss INT(10) NOT NULL,
	  		bossName VARCHAR(255) NOT NULL
	  		) ENGINE = MYISAM, CHARACTER SET utf8 COLLATE utf8_bin;";

        $wpdb->query($structure);
    }

    function wow_armory_table_uninstall()
    {
        global $wpdb;

        $table = $wpdb->base_prefix . "wow_armory_table_chars";
        $structure = "drop table if exists $table";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_classes";
        $structure = "drop table if exists $table";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_groups";
        $structure = "drop table if exists $table";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_groups_mapping";
        $structure = "drop table if exists $table";

        $wpdb->query($structure);

        $table = $wpdb->base_prefix . "wow_armory_table_progress_mapping";
        $structure = "drop table if exists $table";

        $wpdb->query($structure);

        delete_option( 'wow_arsenal_table_options' );
    }

    function add_wow_armory_table_menu_separator( $position ) {

        global $menu;

        $menu[$position] = array(
            0	=>	'',
            1	=>	'read',
            2	=>	'separator' . $position,
            3	=>	'',
            4	=>	'wp-menu-separator'
        );
    }

    function set_wow_armory_table_menu_separator() {
        do_action( 'init_custom_menu_separator', 30 );
    }

    function wow_armory_table_menu_page()
    {
        add_menu_page( __( 'WoW Armory Table' ), __( 'WoW Armory Table' ), 'manage_options', 'wow-armory-table', array($this, 'wow_armory_table_characters'),plugin_dir_url(__FILE__).'icons/wow_icon_16x16.png', '31' );
        //add_submenu_page( 'wow-armory-table', __("Groups", "wowarmorytable-plugin"), __("Groups", "wowarmorytable-plugin"), 'manage_options', 'wow-armory-table-groups', array($this, 'wow_armory_table_groups') );
    }

    function wow_armory_table_characters()
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if(!isset($_GET['page']) || $_GET['page'] != 'wow-armory-table')
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

        if(isset($_POST['poll_chars']))
        {
            $this->get_chars();
        }
        else if(isset($_POST['update_chars']))
        {
            $this->update_chars();
        }
        else if(isset($_POST['empty_chars']))
        {
            $this->delete_chars();
        }
        else if(isset($_POST['classes_english']))
        {
            $this->classes_in_english();
        }
        else if(isset($_POST['reset_classes']))
        {
            $this->get_classes(get_locale());
        }
        else if(isset($_POST['update_progress']))
        {
            $this->get_guild_progress();
        }

        add_thickbox();

        $options = get_option('wow_arsenal_table_options');
        echo '<div class="wrap">';
        echo '<h2>World of Warcraft - Armory Table</h2>';

        require_once(sprintf("%s/php/class.table.php", dirname(__FILE__)));

        $characterTable = new wow_armory_table_list();
        $characterTable->prepare_items();
        ?>
        <div class="wrap">

            <h3><?php _e("Characters of", "wowarmorytable-plugin"); ?> <?php echo $options['guildname']; ?></h3>

            <form id="characters-filter" method="get">
                <input type="hidden" name="page" value="<?php echo sanitize_text_field(trim($_GET['page'])) ?>" />
                <?php $characterTable->display() ?>
            </form>

        </div>
        <hr />
        <?php

        echo '<table class="form-table">';
        echo '<form method="post" name="poll">';
        echo '<tr valign="top">';
        echo '<th scope="row">'.__("Poll Chars", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="poll_chars" value="'.__("First Character Poll", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("Use it only for the First Poll (Fill out Guild Name, Server and Region First)", "wowarmorytable-plugin").'</p>';
        echo '<th scope="row">'.__("Update Chars", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="update_chars" value="'.__("Update Characters", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("This is for Updating your guilds Characters", "wowarmorytable-plugin").'</p>';
        echo '</td>';
        echo '</tr>';
        echo '<tr valign="top">';
        echo '<th scope="row">'.__("Empty Characters", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="empty_chars" value="'.__("Empty Characters", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("If something went wrong and you would like to empty the whole table, use this.", "wowarmorytable-plugin").'</p>';
        echo '</td>';
        echo '<th scope="row">'.__("Classes in English", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="classes_english" value="'.__("English Classes", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("Use this button to switch to english Classnames.", "wowarmorytable-plugin").'</p>';
        echo '</td>';
        echo '</tr>';
        echo '<tr valign="top">';
        echo '<th scope="row">'.__("Reset Classnames to Bloglanguage", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="reset_classes" value="'.__("Reset Classes", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("Use this button to switch back to classnames in your blog's language.", "wowarmorytable-plugin").'</p>';
        echo '<th scope="row">'.__("Update Guild Progress", "wowarmorytable-plugin").'</th><td>';
        echo '<input type="submit" class="button-primary" name="update_progress" value="'.__("Update Progress", "wowarmorytable-plugin").'" />';
        echo '<p class="description">'.__("Use this button to update your guilds progress.", "wowarmorytable-plugin").'</p>';
        echo '</td>';
        echo '</tr>';
        echo '</form>';
        echo '</table>';

    }

    function get_chars()
    {
        $options = get_option( 'wow_arsenal_table_options' );

        $guild = $options['guildname'];
        $guild = str_replace(' ','%20',$guild);

        $server = $options['server'];
        $server = str_replace(' ','%20',$server);

        $region = $options['region'];
        $region = strtolower($region);

        $this->get_classes(get_locale());

        $guildm_data = file_get_contents('http://'.$region.'.battle.net/api/wow/guild/'.$server.'/'.$guild.'?fields=members');
        $guildm_data = json_decode($guildm_data);
        $members = $guildm_data->members;

        $this->save_chars($members);
    }

    function get_classes($lang = "")
    {
        global $wpdb;

        if($lang == "")
            $lang = get_locale();

        $classes = file_get_contents('http://eu.battle.net/api/wow/data/character/classes?locale='.$lang);
        $classes = json_decode($classes);
        $classes = $classes->classes;

        $tablename = $wpdb->base_prefix . 'wow_armory_table_classes';
        $wpdb->query("TRUNCATE TABLE $tablename");

        foreach ($classes as $class) {
            $wpdb->insert(
                $tablename,
                array(
                    'name' => $class->name,
                    'mask' => $class->mask,
                    'powerType' => $class->powerType,
                    'map' => $class->id
                ));
        }

    }

    function save_chars($members)
    {
        global $wpdb;
        foreach ($members as $guildmember) {
            $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';

            $wpdb->insert(
                $tablename,
                array(
                    'name' => $guildmember->character->name,
                    'rank' => $guildmember->rank,
                    'class' => $guildmember->character->class,
                    'race' => $guildmember->character->race,
                    'gender' => $guildmember->character->gender,
                    'lvl' => $guildmember->character->level,
                    'achievementpoints' => $guildmember->character->achievementPoints
                ));
        }
    }

    function update_chars()
    {
        $options = get_option( 'wow_arsenal_table_options' );

        $guild = $options['guildname'];
        $guild = str_replace(' ','%20',$guild);

        $server = $options['server'];
        $server = str_replace(' ','%20',$server);

        $region = $options['region'];
        $region = strtolower($region);

        $guildm_data = file_get_contents('http://'.$region.'.battle.net/api/wow/guild/'.$server.'/'.$guild.'?fields=members');
        $guildm_data = json_decode($guildm_data);
        $members = $guildm_data->members;

        global $wpdb;

        $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
        foreach($members as $guildmember)
        {
            $wpdb->update(
                $tablename,
                array(
                    'rank' => $guildmember->rank,
                    'class' => $guildmember->character->class,
                    'race' => $guildmember->character->race,
                    'gender' => $guildmember->character->gender,
                    'lvl' => $guildmember->character->level,
                    'achievementpoints' => $guildmember->character->achievementPoints
                ),
                array(
                    'name' => $guildmember->character->name
                ));
        }
    }

    function get_guild_progress()
    {
        $options = get_option( 'wow_arsenal_table_options' );

        $guild = $options['guildname'];
        $guild = str_replace(' ','%20',$guild);

        $server = $options['server'];
        $server = str_replace(' ','%20',$server);

        $region = $options['region'];
        $region = strtolower($region);

        global $wpdb;

        $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
        $members = $wpdb->get_results("SELECT * FROM $tablename");

        foreach($members as $member)
        {
            $prog_data = file_get_contents('http://'.$region.'.battle.net/api/wow/character/'.$server.'/'.$member->name.'?fields=progression&locale='.get_locale());
            $prog_data = json_decode($prog_data);
            $raids = $prog_data->progression->raids;

            foreach($raids as $raid)
            {
                foreach($raid->bosses as $boss)
                {
                    if(!isset($boss->normalKills)) $boss->normalKills = 0;
                    if(!isset($boss->heroicKills)) $boss->heroicKills = 0;

                    $progresstable = $wpdb->base_prefix . 'wow_armory_table_progress_mapping';
                    $update = $wpdb->update(
                        $progresstable,
                        array(
                            'raidName' => $raid->name,
                            'bossName' => $boss->name,
                            'normalRaid' => $raid->normal,
                            'heroicRaid' => $raid->heroic,
                            'normalBoss' => $boss->normalKills,
                            'heroicBoss' => $boss->heroicKills
                        ),
                        array(
                            'bossId' => $boss->id
                        ));

                    if($update == 0)
                    {
                        $wpdb->insert(
                            $progresstable,
                            array(
                                'characterId' => $member->id,
                                'raidId' => $raid->id,
                                'raidName' => $raid->name,
                                'normalRaid' => $raid->normal,
                                'heroicRaid' => $raid->heroic,
                                'bossId' => $boss->id,
                                'normalBoss' => $boss->normalKills,
                                'heroicBoss' => $boss->heroicKills,
                                'bossName' => $boss->name
                            ));
                    }
                }
            }
        }
    }

    function display_guild_progress()
    {
        global $wpdb;
        $progresstable = $wpdb->base_prefix . 'wow_armory_table_progress_mapping';
        $raids = $wpdb->get_results("SELECT * FROM $progresstable GROUP BY raidName ORDER BY raidId DESC LIMIT 5");

        foreach($raids as $raid)
        {
            echo "<h3>".$raid->raidName . "</h3><br />";
            $bosses = $wpdb->get_results("SELECT * FROM $progresstable WHERE raidId = $raid->raidId GROUP BY bossId");

            foreach($bosses as $boss)
            {
                echo "<h4>".$raid->bossName . "</h4><br />";
            }
        }
    }

    function delete_chars()
    {
        global $wpdb;

        $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
        $wpdb->query("TRUNCATE TABLE $tablename");
    }

    function classes_in_english()
    {
        global $wpdb;

        $tablename = $wpdb->base_prefix . 'wow_armory_table_classes';
        $wpdb->query("TRUNCATE TABLE $tablename");

        $this->get_classes('en_EN');
    }

    function get_single_character($args, $content = null)
    {
        global $wpdb;
        $options = get_option('wow_arsenal_table_options');
        $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
        $character = $wpdb->get_results("SELECT * FROM $tablename WHERE name='".$content."'", ARRAY_A);
        return "<a class='wowclass-".$character[0]["class"]."' style='font-weight:bold;' href='http://".$options['region'].".battle.net/wow/".$options['language']."/character/".$options['server']."/".$character[0]["name"]."/advanced' target='_blank'>".$content."</a></span>";
    }

}

new wow_armory_table();


?>