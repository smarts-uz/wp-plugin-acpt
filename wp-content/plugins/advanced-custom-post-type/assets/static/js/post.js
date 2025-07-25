var $ = jQuery.noConflict();

jQuery(function() {

    $('.acpt-create-post-modal').each(function () {

        const $this = $(this);
        const button = $this.find("button");
        const fieldId = button.data("field-id");
        const entityType = button.data("entity-type");
        const entityValue = button.data("entity-value");
        const entityId = button.data("entity-id");
        const input = $this.find("input");

        // enable/disable button
        input.on("keyup", function(e){
            if(e.target.value !== ""){
                button.attr("disabled", false);
            } else {
                button.attr("disabled", true);
            }
        });

        // ajax call
        button.on("click", function(){

            const savedValues = $(`input[data-conditional-rules-id="${fieldId}"]`).val();
            console.log(savedValues);

            const payload = {
                value: input.val(),
                fieldId: fieldId,
                entityType: entityType,
                entityValue: entityValue,
                entityId: entityId,
                savedValues: savedValues
            };

            wpAjaxRequest('createPostAndLinkItAction', payload)
                .then((response) => {
                    return response.json();
                })
                .then((res) => {
                    if(res.success && res.success === true){
                        $.modal.close();
                        input.val("");
                        button.attr("disabled", true);
                        location.reload(true);
                    } else {
                        console.error(res);
                    }
                })
            ;
        });
    });
});
