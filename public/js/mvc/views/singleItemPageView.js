window.SingleItemPageView = Backbone.View.extend({
    
    initialize: function(){
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
        'click a.button_date'       : 'openInputDate',
        'change .inputDate'         : 'updateDate',
        'click .button-option-plus' : 'plusButton',
        'click .button-option-gps'  : 'addLocation',
        'pageshow'                  : 'addInputFocus'
    },

    addInputFocus: function(e){
        var $input = this.$el.find('input[type="text"],select').not('input[type=hidden]');
        if ($input.length > 0){
            $input.first().focus();
        }        
    },

    plusButton: function(event){
        if ($(event.currentTarget).hasClass('plus_book_name')){
            this.searchAmazon(this.$el.find('input').val());
        };
        
    },
    
    addLocation: function(){
        navigator.geolocation.getCurrentPosition( 
            this.saveLocation,
            this.errorLocalization,
            {maximumAge:5000, timeout:2000}
        );
    },
    
    errorlocalization: function(){
        
    },
    
    saveLocation: function(currentPosition){
        $('input[name="location[latitude]"]').val(
            currentPosition.coords.latitude
        );
        $('input[name="location[longitude]"]').val(
            currentPosition.coords.longitude
        );
        $('img.button-option-gps').attr('src','/images/icons/other/icon-gps-saved.jpg').addClass('saved');
    },
    
    searchAmazon: function(book){
        
    },
    
    saveEvent: function(){
        $('#add_event').trigger('submit');
    },
    
    updateDate:function(e){
        
        var date = $(e.currentTarget).attr('value');
        
        //update element for saving
        $('#date').attr('value',date);

        //update element for showing
        $.ajax({
            url: '/events/ajax/datelocale/dateW3C/'+date+'/format/json',
            success: function(dateString){
                $('.button_date').replaceWith('<a class="button_date">'+dateString.date+'</a>');                
                $(e.currentTarget).replaceWith('<a class="button_date">'+dateString.date+'</a>');                
            },
            dataType: 'json'
        });
    },
    
    openInputDate:function(e){
        e.preventDefault();
        $(e.currentTarget).replaceWith('<input type="date" value="'
            +$('#date').attr('value')+'" class="inputDate"/>').focus();
    }
});

