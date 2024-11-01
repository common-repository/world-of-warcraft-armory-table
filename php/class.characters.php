<?php
if(!class_exists('wow_armory_table_characters'))
{
    class wow_armory_table_characters
    {
        public function __construct()
        {
            add_shortcode('wow-arsenal-characters', array($this, 'shortcode_characters'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_jquery'));

        }

        public function enqueue_jquery()
        {
            wp_enqueue_script('jquery');

            $url = plugin_dir_url(__FILE__);
            wp_enqueue_script('datatables', $url . '../js/jquery.dataTables.js');
        }

        public function get_all_characters($includeranks = '', $excluderanks = '', $fromlvl = '', $orderby = '')
        {
            global $wpdb;

            $tablename = $wpdb->base_prefix . 'wow_armory_table_chars';
            $classtable = $wpdb->base_prefix . 'wow_armory_table_classes';

            $query = "SELECT $tablename.*, ".$classtable.".name AS classname FROM $tablename, $classtable WHERE ".$tablename.".class = ".$classtable.".map";

            if($includeranks != "")
                $query .= " AND  ".$tablename.".rank IN (".$includeranks.")";

            if($excluderanks != "")
                $query .= " AND  ".$tablename.".rank NOT IN (".$excluderanks.")";

            if($fromlvl != "")
                $query .= " AND  ".$tablename.".lvl >= ".$fromlvl;

            if($orderby != "")
                $query .= " ORDER BY ".$orderby;

            return $wpdb->get_results($query);
        }

        public function get_classnames()
        {
            global $wpdb;

            $classtable = $wpdb->base_prefix . 'wow_armory_table_classes';
            $query = "SELECT * FROM $classtable";
            return $wpdb->get_results($query);
        }

        public function shortcode_characters($args = array())
        {
            extract(shortcode_atts(array (
                'orderby' => 'rank',
                'id' => ''
            ), $args, 'wow-armory-table'));

            if(isset($args['ranks']))
                $rankargs = $args['ranks'];
            else
                $rankargs = '';

            if(isset($args['exranks']))
                $exrankargs = $args['exranks'];
            else
                $exrankargs = '';

            if(isset($args['lvl']))
                $lvlargs = $args['lvl'];
            else
                $lvlargs = '';

            if(isset($args['orderby']))
                $orderbyargs = $args['orderby'];
            else
                $orderbyargs = '';

            if(isset($args['id']))
                $id = $args['id'];
            else
                $id = "";

            if(is_array($args) && in_array('nopage', $args))
                $nopage = "false";
            else
                $nopage = "true";

            if(is_array($args) && in_array('nosearch', $args))
                $nosearch = "false";
            else
                $nosearch = "true";

            if(is_array($args) && in_array('nosort', $args))
                $nosort = "false";
            else
                $nosort = "true";

            if(is_array($args) && in_array('noinfo', $args))
                $noinfo = "false";
            else
                $noinfo = "true";

            if(is_array($args) && in_array('nodetails', $args))
            {
                $noinfo = "false";
                $nosort = "false";
                $nosearch = "false";
                $nopage = "false";
            }

            $options = get_option('wow_arsenal_table_options');

            if(isset($args['number']))
                $length = $args['number'];
            else if(isset($options['entries']))
                $length = $options['entries'];
            else
                $length = 10;

            $url = plugin_dir_url(__FILE__);

            $characters = $this->get_all_characters($rankargs, $exrankargs, $lvlargs, $orderbyargs);

            $ranks = $options['ranks'];

            if(isset($args['title']))
                $title = $args['title'];
            else
                $title = __("Characters of", "wowarmorytable-plugin") ." ". $options['guildname'];

            $output = "";

            if($title != "")
                $output .= '<h3>'.$title.'</h3>';

            if(isset($args['display']) && $args['display'] == "class")
            {
                $classes = $this->get_classnames();
                $output .= $this->class_output($characters, $ranks, $classes);
            }
            else
            {
                $output .= $this->normal_output($characters, $ranks, $id, $nopage, $nosearch, $noinfo, $nosort, $length);
            }

            wp_enqueue_style( 'wowarmorystyle', $url .  '../css/wow-armory-table.css' );

            add_action('wp_footer', array($this, 'custom_style'), 100);

            return $output;
        }

        public function custom_style()
        {
            $options = get_option('wow_arsenal_table_options');
            echo '<style>';
            for($i = 0;$i < count($options['classcolors']);$i++)
            {
                echo '.wow-armory-table .wowclass-'.($i+1).' {';
                echo 'color: '.$options['classcolors'][$i];
                echo '}';
            }
            echo 'table.dataTable tr.odd {';
            echo 'background-color: '.$options['normaltablecolors'][0];
            echo '}';

            echo 'table.dataTable tr.even {';
            echo 'background-color: '.$options['normaltablecolors'][1];
            echo '}';

            echo 'table.dataTable tr.odd td.sorting_1 {';
            echo 'background-color: '.$options['normaltablecolors'][0];
            echo '}';

            echo 'table.dataTable tr.even td.sorting_1 {';
            echo 'background-color: '.$options['normaltablecolors'][1];
            echo '}';

            echo 'table.wow-armory-table , .wow-armory-table td {';
            echo 'border-bottom: 1px solid '.$options['normaltablecolors'][2].'!important;';
            echo '}';

            if($options['classtablecolors'][0] != "")
            {
                echo '.characters-by-class table tr.odd {';
                echo 'background-color: '.$options['classtablecolors'][0].';';
                echo '}';
            }

            if($options['classtablecolors'][1] != "")
            {
                echo '.characters-by-class table tr.even {';
                echo 'background-color: '.$options['classtablecolors'][1].';';
                echo '}';
            }

            if($options['classtablecolors'][2] != "")
            {
                echo '.characters-by-class, .characters-by-class table {';
                echo 'color: '.$options['classtablecolors'][2].';';
                echo '}';
            }

            if($options['classtablecolors'][3] != "")
            {
                echo '.characters-by-class-container {';
                echo 'background-color: '.$options['classtablecolors'][3].';';
                echo '}';
            }

            if($options['classtablecolors'][4] != "")
            {
                echo '.characters-by-class-container hr {';
                echo 'background-color: '.$options['classtablecolors'][4].';';
                echo '}';
            }

            echo '</style>';

        }

        private function normal_output($characters, $ranks, $id, $nopage, $nosearch, $noinfo, $nosort, $length)
        {

            $url = plugin_dir_url(__FILE__);
            wp_enqueue_style( 'wowarmorytablestyles', $url .  '../css/jquery.dataTables.css' );

            $output = '<table id="wow-armory-table'.$id.'" class="wow-armory-table"><thead><tr><th>'.__("Rank", "wowarmorytable-plugin").'</th><th>'.__("Name", "wowarmorytable-plugin").'</th><th>'.__("Level", "wowarmorytable-plugin").'</th><th>'.__("Class", "wowarmorytable-plugin").'</th><th>'.__("Rank", "wowarmorytable-plugin").'</th></tr></thead>';

            foreach ($characters as $character) :
                $output .= '<tr id="'.$character->name.'" class="wowclass-'.$character->class.'">';
                $output .= '<td style="vertical-align:middle;padding:10px">';
                if(isset($ranks[$character->rank]) && $ranks[$character->rank] != "") $output .= $ranks[$character->rank];
                else $output .= $character->rank;
                $output .= '</td>';
                $output .= '<td style="vertical-align:middle;padding:10px">'.$character->name.'</td>';
                $output .= '<td style="vertical-align:middle;padding:10px">'.$character->lvl.'</td>';
                $output .= '<td style="vertical-align:middle;padding:10px">'.$character->classname.'</td>';
                $output .= '<td style="vertical-align:middle;padding:10px">'.$character->rank.'</td>';
                $output .= '</tr>';
            endforeach;

            $output .= '<tfoot></tfoot>';
            $output .= '</table><br />';

            $output .= "<script>";
            $output .= 'jQuery(document).ready(function(){
                            jQuery("#wow-armory-table'.$id.'").dataTable({
                                "bPaginate": '.$nopage.',
                                "bFilter": '.$nosearch.',
                                "bSort": '.$nosort.',
                                "bInfo": '.$noinfo.',
                                "iDisplayLength": '.$length.',
                                "aoColumns": [
                                    { "iDataSort": 4, "sType": "numeric" },
                                    null,
                                    null,
                                    null,
                                    { "bVisible": false}
                                ],
                                "oLanguage": {
                                    "sProcessing":  "'.__("Please Wait...", "wowarmorytable-plugin").'",
                                    "sLengthMenu":  "'.__("Show _MENU_ entries.", "wowarmorytable-plugin").'",
                                "sZeroRecords":     "'.__("No entries present.", "wowarmorytable-plugin").'",
                                "sInfo":            "'.__("_START_ to _END_ from _TOTAL_ entries", "wowarmorytable-plugin").'",
                                "sInfoEmpty":       "'.__("0 to 0 from 0 entries", "wowarmorytable-plugin").'",
                                "sInfoFiltered":    "'.__("(filtered from _MAX_  entries)", "wowarmorytable-plugin").'",
                                "sInfoPostFix":  "",
                                "sSearch":          "'.__("Search", "wowarmorytable-plugin").'",
                                "sUrl":          "",
                                "oPaginate": {
                                        "sFirst":       "'.__("First", "wowarmorytable-plugin").'",
                                        "sPrevious":    "'.__("Previous", "wowarmorytable-plugin").'",
                                        "sNext":        "'.__("Next", "wowarmorytable-plugin").'",
                                        "sLast":        "'.__("Last", "wowarmorytable-plugin").'"
                                    }
                                }
                            });
                      });';
            $output .= "</script>";

            return $output;
        }

        private function class_output($characters, $ranks, $classes)
        {
            add_action( 'wp_footer', array($this, 'widthcalc_script'), 500 );

            $options = get_option('wow_arsenal_table_options');
            $output = "<div class='characters-by-class-container'>";

            foreach($classes as $class)
            {
                $output .= "<div class='characters-by-class'>";
                $output .= "<div class='class-small class-".$class->map."'></div><b>".$class->name."</b><hr />";
                $chars = $this->filter_chars_by_class($class, $characters);
                $output .= "<table>";
                $i = 0;
                foreach($chars as $char)
                {
                    if($i == 0) $cssclass = "odd";
                    else $cssclass = "even";

                    $output .= "<tr class='".$cssclass."'><td width='100'>".$char->name."</td>";
                    $output .= "<td width='60'>";
                    if(isset($ranks[$char->rank]) && $ranks[$char->rank] != "") $output .= $ranks[$char->rank];
                    else $output .= $char->rank;
                    $output .= "</td>";
                    $output .= "<td width='20'><a href='http://".$options['region'].".battle.net/wow/".$options['language']."/character/".$options['server']."/".$char->name."/advanced' target='_blank'><div class='class-small wow_icon_16x16'></div></a></td>";
                    echo "</tr>";

                    $i++;
                    if($i == 2) $i = 0;
                }

                $output .= "</table>";
                $output .= "</div>";
            }

            $output .= "</div>";

            return $output;
        }

        public function filter_chars_by_class($class, $characters)
        {
            $chars = Array();
            foreach($characters as $char)
            {
                if($class->map == $char->class)
                    $chars[] = $char;
            }

            return $chars;
        }

        public function widthcalc_script()
        {
            $options = $options = get_option('wow_arsenal_table_options');

            $maxWidth = 3;
            if(!empty($options["maxWidth"]));
                $maxWidth = $options["maxWidth"];

            ?>
            <script>
                var divider = 2;
                if(Math.floor(jQuery('.characters-by-class').parent().width()) > 650)
                {
                    divider = "<?php echo $maxWidth; ?>";
                }
                else
                {
                    divider = 2;
                }

                jQuery('.characters-by-class').width(Math.floor(jQuery('.characters-by-class').parent().width() / divider));
            </script>
        <?php
        }
    }
}
?>