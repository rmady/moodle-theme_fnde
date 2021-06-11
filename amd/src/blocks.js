/**
 * FNDE Javascript.
 *
 * @package     theme_fnde
 * @category    string
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */

 define(['jquery'], function($) {
    $(document).ready(function() {
        $("#hide-blocks").click(function() {
            $("#region-main-box").toggleClass("blocks-column-collapsed");
            $("#region-main").toggleClass("has-blocks");
            if ($("#region-main-box").hasClass("blocks-column-collapsed")) {
                $(this).text("Exibir blocos");
            } else {
                $(this).text("Ocultar blocos");
            }
            return false;
        });
    });
});