window.SaveEventPageBodyView = Backbone.View.extend({
    
    initialize: function(){ 
        
        this.form = $('#add_event');
        this.initForm();   
        this.initItemPages();
        this.initButtons();
        this.initFocus();    
    },
    
    initFocus: function(){
        $('div[data-role=page]').first()
            .find('input')
            .not('input[type=hidden]').first().focus();
    },
    
    initForm: function(){
        //input for submit post
        if ($('input[type=submit]').length > 0){
            
            this.form.append('<input type="hidden" value="'
                + $('input[type=submit]').attr('value')
                +'" name="' + $('input[type=submit]').attr('name')
                + '"/>');
            $('input[type=submit]').remove();
        }

        //remove input element where tags are entered (but not stored) and 
        // button date
        this.form.on('submit',function(){

            $('input[type=date].popupinput').remove();
            $('input.form-element-tags').remove();
            $('input.form-template').remove();

        });
    },
    
    initButtons: function(){
        
        $('#form_element_item_location').find('div.ui-block-b').append(
            $('<img class="button-option-plus" src="/images/icons/other/icon-plus.png" alt="plus" class="ui-li-icon">')
        );
    },
    
    initItemPages: function(){
        $('div[data-role="page"]').each(function(){
        
            new SingleItemPageView({
                el:this
            });
        });
    }
    
});

