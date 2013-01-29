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
        
        $('input[data-property-name="book_name"]').each(function(index,el){
            $(el).parent().append(
                $('<img src="/images/icons/other/icon-plus.png" alt="plus" class="button-option-plus plus_'
                    +$(el).attr('data-property-name') 
                    +'">')
            );
        });
        
        
        $('input[data-property-name="location"]').each(function(index,el){
            $(el).parent().append(
                $('<img src="/images/icons/other/icon-gps-notconnected.jpg" alt="gps" class="button-option-gps">')
            );
        });
        if (navigator.geolocation){
            $('img.button-option-gps').attr('src','/images/icons/other/icon-gps-connected.jpg');
        }
        
    },
    
    initItemPages: function(){
        $('div[data-role="page"]').each(function(){
        
            new SingleItemPageView({
                el:this
            });
        });
    }
    
});

