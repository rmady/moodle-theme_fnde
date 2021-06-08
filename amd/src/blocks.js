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
            console.log("teste");
            $("#block-region-side-pre").toggleClass("active");
        });
    });
});