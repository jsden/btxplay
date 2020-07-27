var Custom = BX.namespace('Custom');

// Set the router URL
Custom.ROUTER_URL = '/router.php';

Custom.showCommentDialog = function(dealId) {
    var dialog = new BX.CDialog({
        title: BX.message('ENTER_COMMENT'),
        head: BX.message('DEAL_FAILURE_DESC'),
        content: '<form method="POST" style="overflow:hidden;">\
      <textarea id="failure-comment-megakaban" style="height: 78px; width: 374px;"></textarea>\
      </form>',
        icon: 'head-block',
        resizable: true,
        draggable: true,
        height: '168',
        width: '400',
    });

    dialog.SetButtons(
        [
            {
                'title': BX.message('SAVE'),
                'id': 'action_send',
                'name': 'action_send',
                'action': function(){
                    Custom.saveComment(dealId, $('#failure-comment-megakaban').val());
                    this.parentWindow.Close();
                }
            },
            BX.CDialog.btnClose,
        ]
    );

    dialog.Show();
};

/**
 * Save dat comment
 *
 * @param id
 * @param comment
 */
Custom.saveComment = function(id, comment)
{
    $.post(
        Custom.ROUTER_URL, {
            mode: 'update_deal_comment',
            id: id,
            comment: comment
        }
    );
};

/**
 * Called from init.php
 *
 */
Custom.bindEvents = function() {
    /**
     * Set time to 6:00pm if not set
     *
     * @param popup
     */
    var checkResetTime = function(popup) {
        var timeInputs = popup.getElementsByTagName('input');

        // Set time to 18:00 if not set
        if (timeInputs.length === 2) {
            if ((timeInputs[0].value === '00') && (timeInputs[1].value === '00')) {
                timeInputs[0].value = '18';
                timeInputs[1].value = '00';
            }
        }
    };

    /**
     * Try guessing the entity id
     *
     * @returns {int}
     */
    var getEntityIdFromPopup = function()
    {
        var id = null;

        $('.popup-window').each(function() {
            if (matches = $(this).attr('id').match(/deal_(\d+)_/i)) {
                id = matches[1];
            }
        });

        return id;
    };

    /**
     * Append the comment field to the popup when changing deal's stage to failed
     *
     * @param popup
     */
    var appendDealComment = function(popup) {
        var commentField = $('<div><input type="text" id="failure-comment" class="crm-entity-widget-content-input" placeholder="Комментарий"></div>'),
            comment = '',
            dealId = getEntityIdFromPopup();

        if (!dealId) {
            return;
        }

        // Track comment's value
        commentField
            .find('input')
            .bind(
                'change',
                function() {
                    comment = $(this).val();
                }
            );

        // Add comment field
        popup
            .find('.crm-list-end-deal-block-section')
            .append(commentField);

        // Save a comment when OK button is pressed
        popup
            .find('.popup-window-button-accept')
            .bind(
                'click',
                function() {
                    Custom.saveComment(dealId, comment);
                }
            );
    };

    /**
     * Route requests based based on the given id
     *
     * @param id
     * @returns {{add: add}}
     */
    var route = function(id) {
        var popup = $(`#${id}`);

        return {
            add: function(re, cb) {
                if (re.test(id)) {
                    cb(popup);
                }

                return this;
            }
        };
    };

    // Add comment field inside the popup
    BX.addCustomEvent('bx.main.popup:onshow', BX.delegate(function(data) {
        route(data.uniquePopupId)
            .add(/(PROGRESS_BAR_DEAL_.*_FAILURE)|(entity_progress_FAILURE)/, appendDealComment)
            .add(/calendar_popup_.*/, checkResetTime);
    }));

    // Show a separate popup dialog to ask for a comment
    BX.addCustomEvent('Kanban.DropZone:onBeforeItemCaptured', function(event) {
        if ((event.dropZone.data.type === 'LOOSE') && (/\/deal\//.test(event.item.data.link))) {
            Custom.showCommentDialog(event.item.data.id);
        }
    });
};