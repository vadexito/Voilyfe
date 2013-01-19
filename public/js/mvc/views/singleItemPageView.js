window.SingleItemPageView = Backbone.View.extend({
    
    initialize: function(){
    
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
    },
    
    openPopup:function(e){
        var $popup = $($(e.currentTarget).attr('href'));
        $popup.find('input').focus();
    },
    
    render: function(){
        
        
        
        return this.el;
    }
    
});

