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
        
        isAlreadyTag: function (value){
            return ($.inArray(value,this.get('autocomplete').data) != -1);
    }
    });


