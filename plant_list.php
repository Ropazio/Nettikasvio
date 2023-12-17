<!DOCTYPE html>
<html>
<?php
require_once "session.php";
?>

<?php
    require_once "general.php";
    get_header();
?>
<body>
    <div class="page_background">

        <?php
            get_main_headline_box();
            get_navi();

            require_once "filter_and_search.php";
            get_filter();
            print_plants_list($_SESSION['search_string'], $_SESSION['colour'], $_SESSION['type']);
        ?>
    </div>
<script type="text/javascript" src="operation.js"></script>
</body>
</html>
