window.Event = Backbone.Model.extend({
        initialize: function() {
            
            this.eventproperties = this.generateProperties();
            this.set('specificProperties',this.eventproperties.specific);
            this.set('commonProperties', this.eventproperties.common);            
            this.set('title', this.eventproperties.date
                +(this.get('title') ? ' - ' +this.get('title'):''));
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
            var self = this;
            
            _.each(this.get('specificProperties'),function(itemData){
                
                if (itemData.value){                    
                    
                    specificProperties.model.add( new ItemInEvent({
                            value   : self.truncateValue(itemData.value,self.get('truncate')),
                            srcIcon : itemData.srcIcon
                    }));

                }
            });  
            if (this.get('latitude') && this.get('longitude')){
                commonProperties.model.add( new ItemInEvent({
                            value       : '',
                            srcIcon     : '/images/icons/other/icon-gps-saved.jpg',
                            iconSize    : '20px',
                            iconClass   : 'icon-gps',
                            eventId     : this.get('id')
                }));
                
            }
            
            
            _.each(this.get('commonProperties'),function(itemData,name){
                
                    if (name == "date"){
                        date = itemData;
                    } else if (itemData.value) {
                        
                        commonProperties.model.add( new ItemInEvent({
                        value   : self.truncateValue(itemData.value,self.get('truncate')),
                        srcIcon : itemData.srcIcon
                }));
                    }
            });
            
            return {
               specific:$(specificProperties.render()).html(), 
               common:$(commonProperties.render()).html(), 
               date:date 
            };
        },
        
        truncateValue: function(value,maxLength){
        if (value.length > maxLength){
            return value.substring(0,maxLength)+'...';
        } else {
            return value;
        }  
    }
        
        
        
        
    });


