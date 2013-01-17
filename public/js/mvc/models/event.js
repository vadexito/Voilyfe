window.Event = Backbone.Model.extend({
        initialize: function() {
            
        },
        defaults:{
            text:'',
            valueForInput:''
        },
        
        generateEventLineProperties: function(){
            var specificProperties = [];
            var commonProperties = [];
            var date;
            
            _.each(this.get('specificProperties'),function(itemData){
                if (itemData.value){
                    specificProperties.push(itemData.value); 
                }
            });            
            
            _.each(this.get('commonProperties'),function(itemData,name){
                    if (name == "date"){
                        date = itemData;
                    } else if (itemData.value) {
                        commonProperties.push(itemData.value);
                    }
            });
            
            return {
               specific:specificProperties, 
               common:commonProperties, 
               date:date 
            };
        }
        
        
        
        
        
        
    });


