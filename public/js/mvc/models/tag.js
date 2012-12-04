window.Tag = Backbone.Model.extend({
        initialize: function() {
            this.on('error',function(model,error){
            });
        },
        defaults:{
            text:'',
            id:0,
            valueForInput:''
        },
        
        validate: function(attributes){
        }
    });


