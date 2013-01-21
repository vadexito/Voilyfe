window.Event = Backbone.Model.extend({
        initialize: function() {
            
            this.eventproperties = this.generateProperties();
            this.set('specificProperties',this.eventproperties.specific);
            this.set('commonProperties', this.eventproperties.common);
            this.set('title', this.eventproperties.date);
            this.set('eventId',this.get('id'));
        },
        defaults:{
            text:'',
            valueForInput:''
        },
        
        generateProperties: function(){
            var specificProperties = new ItemInEventsView({model:new ItemInEvents});
            var commonProperties = new ItemInEventsView({model:new ItemInEvents});
            var date;
            
            
            _.each(this.get('specificProperties'),function(itemData){
                
                if (itemData.value){                    
                    
                    specificProperties.model.add( new ItemInEvent({
                            value   : itemData.value,
                            srcIcon : itemData.srcIcon
                    }));

                }
            });            
            
            _.each(this.get('commonProperties'),function(itemData,name){
                    if (name == "date"){
                        date = itemData;
                    } else if (itemData.value) {
                        
                        commonProperties.model.add( new ItemInEvent({
                        value   : itemData.value,
                        srcIcon : itemData.srcIcon
                }));
                    }
            });
            
            return {
               specific:$(specificProperties.render()).html(), 
               common:$(commonProperties.render()).html(), 
               date:date 
            };
        }
        
        
        
        
        
        
    });


