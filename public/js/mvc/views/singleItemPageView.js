window.SingleItemPageView = Backbone.View.extend({
    
    initialize: function(){
        this.popupDate = this.$el.find('.popupDate');
        this.initTags();
    
    },
    
    initTags: function(){
        var input = this.$el.find('input.form-element-tags');  
        if (input.length > 0){
            new InputView({
                el: input,
                collection: new Tags,
                model: new Input({
                    'tagsContainer': $('#add_event'),
                    'propertyName':$(input).attr('data-property-name'),
                    'multitag':$(input).attr('data-multitag'),
                    'itemName':$(input).attr('data-item-name'),
                    'itemId':$(input).attr('data-containerId'),
                    'itemGroupForm': $('#'+$(input).attr('data-property-name')+'_itemGroup_form_page'),
                    'formElementName': $(input).attr('id'),
                    'autocomplete':$.parseJSON($(input).attr('data-autocomplete')),
                    'populate':$.parseJSON($(input).attr('data-populate'))
                })
            });
        }
    },
    
    
    events:{
        'click a.menu_save'         : 'saveEvent',
        'click a.button_date'       : 'openPopup',
        'change div.popupDate'      : 'updateDate',
        'click .button-option-plus' : 'plusButton',
        'pageshow'                  : 'addInputFocus'
    },

    addInputFocus: function(e){
        this.$el.find('input').not('input[type=hidden]').first().focus();
    },
    
    plusButton: function(){
        console.log('plus');
    },
    
    saveEvent: function(){
        $('#add_event').trigger('submit');
    },
    
    updateDate:function(e){
        var $input = $(e.currentTarget).find('input');
        var date = $input.attr('value');
        
        //update element for saving
        $('#date').attr('value',date);

        //update element for showing
        $.ajax({
            url: '/events/ajax/datelocale/dateW3C/'+date+'/format/json',
            success: function(dateString){

                $('.button_date').text(dateString.date);
            },
            dataType: 'json'
        });
        
        //close popup        
        this.popupDate.popup("close");
        
    },
    
    openPopup:function(e){
        
        this.popupDate.find('input').focus();
    }
});

