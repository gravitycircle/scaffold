<?php
function _seo($page){
ob_start();
?>

<?=$page?>

<?php
return ob_get_clean();
}
?>