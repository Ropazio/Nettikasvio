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
            require_once "main_page.php";
        ?>
    </div>
<script type="text/javascript" src="operation.js"></script>
</body>
</html>