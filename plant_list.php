<!DOCTYPE html>
<html>
<?php
    require_once "general.php";
    get_header();
?>
<body>
    <div class="page_background">

        <?php
            get_main_headline_box();
            get_navi();
        ?>
        <?php
            require_once "filter_and_search.php";
            get_filter();
            echo '<a>' . $parameters[0] . $parameters[1]. '</a>';

            //apply_filters_and_get_plants_list($parameters[0], $parameters[1]);
            //print_plants_list();
        ?>
    </div>
<script type="text/javascript" src="operation.js"></script>
</body>
</html>
