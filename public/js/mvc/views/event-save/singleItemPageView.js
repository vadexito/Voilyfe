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
        'click a.menu_save'                 : 'saveEvent',
        'click a.button_date'               : 'openInputDate',
        'change .inputDate'                 : 'updateDate',
        'click .button-option-plus'         : 'plusButton',
        'click .button-option-gps.unsaved'  : 'addLocation',
        'pageshow'                          : 'addInputFocus',
        'click a.close-popup'               : 'saveLocationInput'
        
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
    
    addLocation: function(e){
        var self = this;
        var zoom = 15;
        
        var openMap = function(position){
            
            new mylife().showPopupMap(
                self.$el.attr('id'),
                'choosemap',
                {
                    id:'map_choose'+self.$el.attr('id'),
                    latitude: position.coords.latitude, 
                    longitude: position.coords.longitude,
                    self:self,
                    zoom: zoom
                },
                self.showMapFormChoosing
            );
        };
        
        // if a position if registrered (edit event) use it (populate map)
        var latitude = $('input[name="location[latitude]"]').val();
        var longitude = $('input[name="location[longitude]"]').val();
        if (latitude > 0 && longitude > 0){
            
            openMap({coords:{latitude:latitude ,longitude:longitude}});  
            console.log('k');
        } else {
            
            var errorLocalization = function(error){
                console.log(error.code);
                zoom = 5;
                var paris = {coords:{
                        latitude:48.857487,
                        longitude:2.342834
                }};
                openMap(paris);
            };

            // if there is a current position available use it otherwise center 
            // on an arbitrary given city
            navigator.geolocation.getCurrentPosition( 
                openMap,
                errorLocalization,
                {maximumAge:5000, timeout:2000}
            );
        }
    },
    
    showMapFormChoosing: function(options){
        //create map
        var centerPosition = new google.maps.LatLng(options.latitude,options.longitude);
        
        var map = new google.maps.Map($('#'+options.id).get(0), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: centerPosition ,
            zoom: options.zoom ? options.zoom : 15
        });
        
        // add marker
        options.self.marker = new google.maps.Marker({
            position: centerPosition,
            map: map,
            draggable:true
        });
    },
    
    saveLocationInput: function(){
        
        this.saveLocationInDataBase(this.marker.getPosition());
    },
    
    saveLocationInDataBase: function(googleLatLng){
        
        $('input[name="location[latitude]"]').val(googleLatLng.lat());
        $('input[name="location[longitude]"]').val(googleLatLng.lng());
        
        $('img.button-option-gps').attr('src','/images/icons/other/icon-gps-saved.jpg');
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

