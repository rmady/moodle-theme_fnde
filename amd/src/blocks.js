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
        if (sessionStorage.getItem('isBlocksHide')) {
            let hideBlocks = sessionStorage.getItem('isBlocksHide');
            if (hideBlocks === "true") {
                $("#region-main-box").addClass("blocks-column-collapsed");
                $("#region-main").removeClass("has-blocks");
                $("#hide-blocks").text("Exibir blocos");
            }
        } else if (!$("body#page-my-index").length && $("body").not(".mobiletheme").length) {
            $("#region-main-box").addClass("blocks-column-collapsed");
            $("#region-main").removeClass("has-blocks");
            $("#hide-blocks").text("Exibir blocos");
        }
        $("#hide-blocks").click(function() {
            $("#region-main-box").toggleClass("blocks-column-collapsed");
            $("#region-main").toggleClass("has-blocks");
            if ($("#region-main-box").hasClass("blocks-column-collapsed")) {
                $(this).text("Exibir blocos");
                sessionStorage.setItem("isBlocksHide", true);
            } else {
                $(this).text("Ocultar blocos");
                sessionStorage.setItem("isBlocksHide", false);
            }
            return false;
        });
    });
});