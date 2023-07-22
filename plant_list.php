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
            //apply_filters_and_get_plants_list();
            //print_plants_list();
        ?>
    </div>
<script type="text/javascript" src="operation.js"></script>
</body>
</html>
