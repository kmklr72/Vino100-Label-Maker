/* 
 * @author pawelmech
 * @link http://stackoverflow.com/questions/4430262/manually-type-in-a-value-in-a-select-drop-down-html-list
 */

(function ($) {
    $.fn.otherize = function(option_text, texts_placeholder_text) {
        texts_placeholder_text = typeof texts_placeholder_text !== 'undefined' ? texts_placeholder_text : "";

        var oSel = $(this);
        var option_id = oSel.attr('id') + '_other';
        var textbox_id = option_id + "_tb";

        this.append("<option value='' id='" + option_id + "' class='otherize' >" + option_text + "</option>");
        this.after("<input type='text' id='" + textbox_id + "' style='display: none; width: 200px;' placeholder='" + texts_placeholder_text + "'/>");
        this.change(
            function() {
                var oTbox = oSel.parent().children('#' + textbox_id);
                oSel.children(':selected').hasClass('otherize') ? oTbox.show() : oTbox.hide();
            }
        );

        $("#" + textbox_id).change(
            function() {
                $("#" + option_id).val($("#" + textbox_id).val());
            }
        );
    };
}(jQuery));