window.Input = Backbone.Model.extend({
    
        initialize: function() {
        },
        
        defaults: {
            'tagsContainer': '',
            'propertyName':'',
            'formElementName':'',
            'autocomplete':{data:[]},
            'inputTag':''
        },
        
        getIdTag: function (tag){
            
            $.each(this.get('autocomplete').data,function(key,value){
                if (value.label.toLowerCase() == tag.toLowerCase()){
                    return key;
                }
            });
            
            return false;
    }
    });


